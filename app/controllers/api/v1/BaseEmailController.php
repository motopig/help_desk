<?php

/**
 * 邮件控制器
 * @package \App\Controllers\Api\V1
 * @author no<no>
 * @copyright ©2012 - 2014 no. All rights reserved.
 */
class BaseEmailController extends Controller
{

    /**
     * 邮件箱地址
     *
     * @var string
     */
    private $mailbox = '';

    /**
     * 邮件箱对象
     *
     * @var object
     */
    private $mBox = null;

    /**
     * 统一编码
     *
     * @var string
     */
    private $stdEnc = 'UTF-8';
    
    /**
     * 收件箱目录
     * @var string
     */
    private $inboxPath = 'INBOX';
    
    /**
     * 垃圾邮箱目录
     * @var string
     */
    private $spamPath = 'SPAM';

    /**
     * 初始构造方法
     */
    public function __construct()
    {}

    /**
     * 实例结束时关闭邮箱
     */
    public function __destruct()
    {
        $this->closeMailBox();
    }

    /**
     * 根据参数打开mailbox
     *
     * @param string $mailbox            
     * @param string $username            
     * @param string $password            
     * @param int $options            
     * @param int $n_retries            
     * @param string $params            
     * @return boolean | IMAP stream
     */
    public function openMailBox($mailbox, $username, $password, $options = 0, 
        $n_retries = 0, $params = array())
    {
        $this->mailbox = $mailbox;
        $this->mBox = imap_open($mailbox, $username, $password, $options, 
            $n_retries, $params);
        
        return $this->mBox;
    }
    
    /**
     * 设置邮件箱目录，暂时支持收件箱(inbox)和垃圾邮箱(spam)
     * @param array $boxPaths
     */
    public function setBoxPath($boxPaths)
    {
        $all = array(
            'inbox', 
            'spam'
        );
        foreach ($boxPaths as $mthd => $val) {
            $mthd = strtolower($mthd);
            if (! in_array($mthd, $all)) {
                continue;
            }
            
            $mthd = 'set' . ucfirst($mthd) . 'Path';
            if (method_exists($this, $mthd) && ! empty($val)) {
                $this->$mthd($val);
            }
        }
        return true;
    }
    
    /**
     * 设置收件箱目录
     * @param string $str
     */
    private function setInboxPath($str)
    {
        $this->inboxPath = $str;
    }
    
    /**
     * 设置垃圾邮箱目录
     * @param unknown $str
     */
    private function setSpamPath($str)
    {
        $this->spamPath = $str;
    }

    /**
     * 关闭邮箱
     */
    public function closeMailBox()
    {
        if (! empty($this->mBox)) {
            imap_close($this->mBox);
            $this->mBox = null;
        }
    }

    /**
     * 获取邮件箱列表 返回邮箱数组: {ttl:转码后显示, val:原值}
     *
     * @return array
     */
    public function fetchBoxList()
    {
        $list = imap_list($this->mBox, $this->mailbox, '*');
        $boxes = array();
        if (is_array($list)) {
            foreach ($list as $val) {
                $tmp = array();
                $tmp['ttl'] = $this->mailDecode($val);
                $tmp['val'] = $val;
                $boxes[] = $tmp;
            }
        }
        
        return $boxes;
    }

    /**
     * 根据参数获取收件箱和垃圾邮件中的邮件 默认取当天邮件
     *
     * @param string $criteria            
     * @return array
     */
    public function fetchMails($criteria = '', $count = 0)
    {
        if (empty($criteria)) {
            // $criteria = 'ON "' . date('d M Y') . '"';
            $criteria = 'ON "' . date('d M Y', time()-43200) . '"';
        }
        
        $mails = array();
        $mails['inbox'] = $this->fetchInboxMailsByCriteria($criteria, $count);
        $mails['spam'] = $this->fetchSpamMailsByCriteria($criteria, $count);
        
        return $mails;
    }

    /**
     * 获取收件箱邮件 默认取新邮件
     *
     * @param string $criteria            
     * @return array
     */
    public function fetchInboxMailsByCriteria($criteria = '', $count = 0)
    {
        $this->openInbox();
        $mails = $this->fetchMailsByCriteria($criteria, $count);
        
        return $mails;
    }

    /**
     * 获取垃圾邮件 默认取新邮件
     *
     * @param string $criteria            
     * @return array
     */
    public function fetchSpamMailsByCriteria($criteria = '', $count = 0)
    {
        $this->openSpambox();
        $mails = $this->fetchMailsByCriteria($criteria, $count);
        
        return $mails;
    }

    /**
     * 获取收件箱文件夹
     *
     * @return string
     */
    public function getInbox()
    {
        $rtn = $this->mailbox . $this->inboxPath;
        
        return $rtn;
    }

    /**
     * 获取垃圾邮件箱文件夹
     *
     * @return string
     */
    public function getSpambox()
    {
        $rtn = $this->mailbox . $this->spamPath;
        
        return $rtn;
    }

    /**
     * 根据条件获取邮件箱中的邮件
     *
     * @param string $box            
     * @param string $criteria            
     * @return array
     */
    public function fetchMailsByCriteria($criteria = '', $count = 0)
    {
        $tmp = imap_search($this->mBox, $criteria);
        if (empty($tmp)) {
            return array();
        }
        
        // 如果count>0，限制邮件数量
        $msgnos = array();
        if ($count > 0) {
            $i = 0;
            foreach ($tmp as $v) {
                if (++ $i > $count) {
                    break;
                }
                $msgnos[] = $v;
            }
        } else {
            $msgnos = $tmp;
        }
        
        // 获取邮件信息，邮件头和邮件体内容
        
        $mails = $this->fetchMailsByMsgno($msgnos);
        
        return $mails;
    }

    /**
     * 进入收件箱
     *
     * @return boolean
     */
    public function openInbox()
    {
        $box = $this->getInbox();
        $rs = $this->reopenBox($box);
        return $rs;
    }

    /**
     * 进入垃圾箱
     *
     * @return boolean
     */
    public function openSpambox()
    {
        $box = $this->getSpambox();
        $rs = $this->reopenBox($box);
        return $rs;
    }

    /**
     * 进入指定邮件箱
     *
     * @param string $box            
     * @return boolean
     */
    private function reopenBox($box)
    {
        $rs = false;
        if (empty($box)) {
            return $rs;
        }
        $rs = imap_reopen($this->mBox, $box);
        return $rs;
    }

    /**
     * 根据匹配条件获取邮件的uid信息
     *
     * @param string $box            
     * @param string $criteria            
     * @return multitype:
     */
    public function fetchUids($criteria = '')
    {
        if (empty($criteria)) {
            // $criteria = 'ON "' . date('d M Y') . '"';
            $criteria = 'ON "' . date('d M Y', time()-43200) . '"';
        }
        
        $uids = imap_search($this->mBox, $criteria, SE_UID);
        return $uids;
    }

    /**
     * 根据传递的uid获取邮件内容
     *
     * @param array $uids            
     */
    public function fetchMailsByUids($uids, $count = 0)
    {
        $mails = array();
        if (empty($uids)) {
            return $mails;
        }
        
        $msgnos = array();
        $i = 0;
        foreach ($uids as $uid) {
            if ($count > 0) {
                if (++ $i > $count) {
                    break;
                }
            }
            
            $msgnos[] = imap_msgno($this->mBox, $uid);
        }
        
        $mails = $this->fetchMailsByMsgno($msgnos);
        
        return $mails;
    }

    /**
     * 通过传递msgno获取邮件内容
     *
     * @param array $msgnos            
     * @return array
     */
    public function fetchMailsByMsgno($msgnos)
    {
        $mails = array();
        if (empty($msgnos)) {
            return $mails;
        }
        
        foreach ($msgnos as $msgno) {
            $mail = new stdclass();
            
            // 根据message number获取邮件uid
            $tmp = imap_uid($this->mBox, $msgno);
            if (empty($tmp)) {
                $tmp = 0;
            }
            $mail->uid = $tmp;
            
            // 获取邮件头信息
            $tmp = $this->fetchHeader($msgno);
            if ($tmp === false) {
                continue;
            }
            
            // 邮件日期
            if (empty($tmp->date)) {
                $mail->date = '';
            }
            
            // 邮件标题
            if (empty($tmp->subject)) {
                continue;
            } else {
                $mail->subject = $tmp->subject;
            }
            
            // 邮件message_id
            if (empty($tmp->message_id)) {
                $mail->message_id = '';
            } else {
                $mail->message_id = $tmp->message_id;
            }
            
            // 邮件来源名称
            if (empty($tmp->fromaddress)) {
                $mail->fromaddress = '';
            } else {
                $mail->fromaddress = $tmp->fromaddress;
            }
            
            // 邮件来源邮箱
            if (empty($tmp->from)) {
                continue;
            } else {
                $mail->from = $tmp->from;
            }
            
            // 邮件抄送名称
            if (empty($tmp->ccaddress)) {
                $mail->ccaddress = '';
            } else {
                $mail->ccaddress = $tmp->ccaddress;
            }
            
            // 邮件抄送地址
            if (empty($tmp->cc)) {
                $mail->cc = '';
            } else {
                $mail->cc = $tmp->cc;
            }
            
            // 邮件message number
            if (empty($tmp->Msgno)) {
                continue;
            } else {
                $mail->msgno = $tmp->Msgno;
            }
            
            // 邮件发送日期
            if (empty($tmp->MailDate)) {
                $mail->maildate = '';
            } else {
                $mail->maildate = $tmp->MailDate;
            }
            
            // 邮件unix时间
            if (empty($tmp->udate)) {
                $mail->udate = '';
            } else {
                $mail->udate = $tmp->udate;
            }
            // $mail->body = $this->fetchBody($msgno);
            // if (empty($mail->body)) {
            //     continue;
            // }

            // 获取邮件内容和属性
            $body = $this->fetchBody($msgno);
            if (empty($body)) {
                continue;
            } else {
                $mail->body = $body['body'];
                $mail->type = $body['type'];
            }
            
            $mail->attachment = $this->fetchAttachment($msgno); // 邮件附件队列
            $mails[$msgno] = $mail;
        }

        return $mails;
    }

    /**
     * 获取邮件头
     *
     * @param int $msgNo            
     * @return array
     */
    public function fetchHeader($msgNo)
    {
        $head = imap_headerinfo($this->mBox, $msgNo);
        
        // 标题解码
        if (! empty($head->subject)) {
            $tmp = imap_mime_header_decode($head->subject);
            $str = '';
            foreach ((array)$tmp as $row) {
                $str .= trim($this->strDecode($row->text, $row->charset));
            }
            $head->subject = trim($str);
        }
        
        if (! empty($head->Subject)) {
            $tmp = imap_mime_header_decode($head->Subject);
            $str = '';
            foreach ((array)$tmp as $row) {
                $str .= trim($this->strDecode($row->text, $row->charset));
            }
            $head->Subject = trim($str);
            if (empty($head->subject)) {
                $head->subject = $head->Subject;
            }
        }
        
        // 防止空标题
        if (empty($head->subject)) {
            return false;
        }

        // 发件人邮箱
        if (! empty($head->from[0])) {
            $tmp = $head->from[0];
            $head->from = $tmp->mailbox . '@' . $tmp->host;
        }
        
        // 发件人解码
        if (! empty($head->fromaddress)) {
            $tmp = imap_mime_header_decode($head->fromaddress);
            $enc = $tmp[0]->charset;
            $str = $tmp[0]->text;
            $str = $this->strDecode($str, $enc);
            $head->fromaddress = $str;
        } else {
            $head->fromaddress = $head->from;
        }

        // 抄送人邮箱
        if (! empty($head->cc)) {
            $arr = array();
            foreach ($head->cc as $tmp) {
                $arr[] = $tmp->mailbox . '@' . $tmp->host;
            }
            $head->cc =  implode(',', $arr);
        } else {
            $head->cc = '';
        }
        
        // 抄送人解码
        if (! empty($head->ccaddress)) {
            $tmp = imap_mime_header_decode($head->ccaddress);
            
            $arr = array();
            foreach ($tmp as $row) {
                $enc = $row->charset;
                $str = $row->text;
                $arr[] = $this->strDecode($str, $enc);
            }
            $head->ccaddress = implode(',', $arr);
        } else {
            $head->ccaddress = $head->cc;
        }
        
        return $head;
    }

    /**
     * 获取邮件内容
     *
     * @param int $msgNo            
     * @return array
     */
    public function fetchBody($msgNo)
    {
        $body = '';
        
        // 邮件体HTML内容
        $tmp = $this->fetchPart($msgNo, '2');
        if (! empty($tmp)) {
            // $body = $tmp;

            // 获取邮件内容和属性
            $body['body'] = $tmp;
            $body['type'] = 'html';
        }
        
        // 邮件体文本内容
        if (empty($body)) {
            $tmp = $this->fetchPart($msgNo, '1');
            if (! empty($tmp)) {
                // $body = $tmp;

                // 获取邮件内容和属性
                $body['body'] = $tmp;
                $body['type'] = 'text';
            }
        }
        
        return $body;
    }

    /**
     * 获取内容体，可进行递归获取
     *
     * @param int $msgNo            
     * @param string $secNo            
     */
    public function fetchPart($msgNo, $secNo = '1')
    {
        // 获取邮件体结构
        $stc = imap_bodystruct($this->mBox, $msgNo, $secNo);
        
        // 获取邮件节点体结构
        $i = 0;
        $max = 4;
        if (empty($stc) || $stc->type != 0) {
            while ((empty($stc) || $stc->type != 0) && ++ $i < $max) {
                $secNo .= '.1';
                $stc = imap_bodystruct($this->mBox, $msgNo, $secNo);
            }
        }
        
        if (empty($stc)) {
            return '';
        }
        
        // 获取邮件体文本内容
        $tmp = imap_fetchbody($this->mBox, $msgNo, $secNo);
        if (empty($tmp)) {
            return '';
        }
        
        // 邮件体内容解码
        $body = $this->bodyDecode($tmp, $stc->encoding);
        
        // 获取字体编码
        $charset = 'UTF-8';
        if (! empty($stc->parameters)) {
            foreach ($stc->parameters as $row) {
                if (strtolower($row->attribute) == 'charset') {
                    $charset = $row->value;
                    break;
                }
            }
        }
        
        // 内容字体解码
        if ($charset !== $this->stdEnc) {
            $body = $this->strDecode($body, $charset);
        }
        
        return $body;
    }
    
    // 邮件附件队列
    public function fetchAttachment($msgNo)
    {
        $attachment = 0;
        $structure = imap_fetchstructure($this->mBox, $msgNo);
        
        if (! empty($structure->parts)) {
            foreach ($structure->parts as $k => $v) {
                // 内部一维数组过滤附件结构
                if ($v->ifdisposition > 0 && $v->ifdparameters > 0) {
                    $attachment = 1;
                } else {
                    // 内部二维数组过滤附件结构
                    if (! empty($v->parts)) {
                        $attachment = $this->fetchAttachmentPart($v->parts);
                    }
                }
            }
        }
        
        return $attachment;
    }
    
    // 多层邮件附件队列
    public function fetchAttachmentPart($part)
    {
        $attachment = 0;
        if (! empty($part)) {
            foreach ($part as $k => $v) {
                if ($v->ifdisposition > 0 && $v->ifdparameters > 0) {
                    $attachment = 1;
                }
            }
        }
        
        return $attachment;
    }

    /**
     * 根据邮件编码和邮件内容进行解码
     *
     * @param string $msg            
     * @param string $encoding            
     * @return string
     */
    public function bodyDecode($msg, $encoding)
    {
        $message = '';
        switch ($encoding) {
            case 0: // 7BIT
                $message = $msg;
                break;
            case 1: // 8BIT
                $message = imap_8bit($msg);
                $message = imap_qprint($message);
                break;
            case 2: // BINARY
                $message = imap_binary($msg);
                break;
            case 3: // BASE64
                $message = imap_base64($msg);
                break;
            case 4: // QUOTED-PRINTABLE
                $message = imap_qprint($msg);
                break;
            case 5: // OTHER
            default:
                $message = $msg;
                break;
        }
        
        return $message;
    }

    /**
     * 邮件内容编码
     *
     * @param string $str            
     * @return string
     */
    public function mailEncode($str)
    {
        $rs = mb_convert_encoding($str, 'UTF7-IMAP', $this->stdEnc);
        return $rs;
    }

    /**
     * 邮件内容解码
     *
     * @param string $str            
     * @return string
     */
    public function mailDecode($str)
    {
        $rs = mb_convert_encoding($str, $this->stdEnc, 'UTF7-IMAP');
        return $rs;
    }

    /**
     * 编码转码
     *
     * @param string $str            
     * @param string $encoding            
     * @return string
     */
    public function strDecode($str, $encoding = 'UTF-8')
    {
        $encoding = strtoupper($encoding);
        if ($encoding === 'UTF8') {
            $encoding = 'UTF-8';
        }
        
        // 韩国棒子稀巴
        if ($encoding === 'KS_C_5601-1987') {
            $encoding = 'GBK';
        }
        
        if ($encoding === 'DEFAULT') {
            $encoding = $this->stdEnc;
        }
        
        if ($encoding === 'GB2312') {
            $encoding = 'GBK';
        }
        
        if ($encoding === 'GBK') {
            $encoding = 'GB18030';
        }
        
        // iconv转中文
        $arrEnc = array(
            'GBK', 
            'GB18030'
        );
        if (in_array($encoding, $arrEnc)) {
            $str = iconv($encoding, $this->stdEnc, $str);
            $encoding = 'UTF-8';
        }
        
        if ($encoding === $this->stdEnc) {
            return $str;
        }
        
        // 获取支持的编码
        $csList = mb_list_encodings();
        foreach ($csList as $k => $v) {
            $csList[$k] = strtoupper($v);
        }
        
        if (! in_array($encoding, $csList)) {
            $encoding = mb_detect_encoding($str);
        }
        
        $str = mb_convert_encoding($str, $encoding, $this->stdEnc);
        
        return $str;
    }

    // 邮件已读设置
    public function setflagFull($uid, $flag)
    {
        $mbox = $this->mBox;
        imap_setflag_full($mbox, $uid, $flag);
    }

    // 邮件附件uid有效期判断
    public function AttachmentMailsByUid($uid)
    {
        $msgnos = array();
        $msgnos[] = imap_msgno($this->mBox, $uid);
        return $msgnos;
    }

    // 邮件附件下载
    public function emailAttachment($savedirpath, $msgno, $uid, $id, $order, $ask, $company_id)
    {
        // 配置文件路径
        $savedirpath = str_replace('\\', '/', $savedirpath);
        if (substr($savedirpath, strlen($savedirpath) - 1) != '/') {
            $savedirpath .= '/';
        }

        // 打开邮箱
        $mbox = $this->mBox;
        $structure = imap_fetchstructure($mbox, $uid, FT_UID); // 获取下载文件信息

        // 处理附件流程
        if (! empty($structure->parts)) {
            $orderData[$id] = $this->emailAttachmentDownload($structure->parts, $mbox, $msgno, $order, $ask, $savedirpath);
        }

        // 添加工单附件ID
        if (count($orderData[$id]) > 0) {
            $enclosure_data = $orderData[$id];
            $enclosure = '';
            foreach ($enclosure_data as $k => $v) {
                $enclosure .= $v . ',';
            }
            
            $enclosure = rtrim($enclosure, ',');
            DB::table($company_id . '_order_ask')->where('id', $id)
            ->update(array('attachment' => 2, 'enclosure' => $enclosure));
        }
    }

    // 邮件附件下载处理
    public function emailAttachmentDownload($parts, $mbox, $msgno, $order, $ask, $savedirpath)
    {
        // 临时附件ID
        $orderData = array();

        // 判断是否需要创建文件目录
        if (!is_dir($savedirpath)) {
            mkdir($savedirpath, 0777, true);
        }

        // 邮件附件体储存
        $part = array();
        foreach ($parts as $ks => $vs) {
            // 第一层过滤附件结构
            if ($vs->ifdisposition > 0 && $vs->ifdparameters > 0) {
                $part[$ks] = $vs;
                $part[$ks]->layer = 1;
            } else {
                // 第二层过滤附件结构
                if (isset($vs->parts)) {
                    foreach ($vs->parts as $pks => $pvs) {
                        if ($pvs->ifdisposition > 0 && $pvs->ifdparameters > 0) {
                            $part[$pks] = $pvs;
                            $part[$pks]->layer = 2;
                        }
                    }
                }
            }
        }

        // 邮件附件体下载
        if (count($part) > 0) {
            foreach ($part as $k => $v) {
                // 邮件附件名称编码过滤
                $dparameters_value = '';
                foreach ($v->dparameters as $dk => $dv) {
                    if ($dv->attribute == 'filename') {
                        $dparameters_value = imap_utf8($dv->value);
                        break;
                    }
                }

                if ($dparameters_value == '') {
                    $dparameters_value = imap_utf8($v->dparameters[0]->value);
                }

                if ($v->encoding == 3 && $dparameters_value != '') {
                    if (preg_match('/=\?([a-zA-z0-9\-]+)\?(.*)\?=/i', $dparameters_value, $match)) {
                        if (strtolower(substr($match[1], 0, 2)) == 'gb') {
                            $dparameters_value = mb_convert_encoding(imap_utf8($dparameters_value), "UTF-8", "GBK");
                        }
                    }
                }

                // 文件名称组合流程
                $client_original_name = explode('.', $dparameters_value); // 获取文件名
                $end_name = $client_original_name[count($client_original_name) - 1]; // 获取后缀
                $extension = $end_name;
                $body_name = explode($end_name, $dparameters_value); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $filename = $start_name . '_' . str_random(4) . '.' . $end_name;

                switch ($extension) {
                    case 'png':
                        $file_type = 'image';
                        break;
                    case 'jpg':
                        $file_type = 'image';
                        break;
                    case 'jpeg':
                        $file_type = 'image';
                        break;
                    case 'gif':
                        $file_type = 'image';
                        break;
                    case 'bmp':
                        $file_type = 'image';
                        break;
                    default:
                        $file_type = 'file';
                        break;
                }

                // 判断附件层结构体
                if ($v->layer == 1) {
                    $mege = imap_fetchbody($mbox, $msgno, $k+1);
                } elseif ($v->layer == 2) {
                    $mege = imap_fetchbody($mbox, $msgno, '2.'.($k+1));
                }
                
                $data = $this->bodyDecode($mege, $v->encoding);
                if (is_dir($savedirpath)) {
                    $fp = fopen($savedirpath . $filename, "w");
                    if (fputs($fp, $data)) {
                        // 记录附件信息
                        $encl = new Enclosure();
                        $encl->enclosure_name = $dparameters_value;
                        $encl->type = $file_type;
                        $encl->suffix = $extension;
                        $encl->path = $savedirpath . $filename;
                        $encl->ask = $ask;
                        $encl->order = $order;
                        
                        if ($encl->save()) {
                            $orderData[] = $encl->id;
                        }
                    }

                    fclose($fp);
                }
            }
        }

        return $orderData;
    }
}
