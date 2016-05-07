<?php

class QuereEmailController extends Controller 
{
    // 邮件指定阀值
    private $page = 30;

	// 工单邮件内容发送脚本处理
	// 脚本地址：http://localhost/ecdesk/public/api/v1/contentquere
	// 每分钟执行命令：*/1 * * * * /usr/bin/curl http://localhost/ecdesk/public/api/v1/contentquere
	public function contentQuere()
   	{
        // 获取邮件队列列表
        $EmailContent = Quere::orderBy('created_at', 'asc')->where('execute', '=', '0')->paginate(10);
        if (count($EmailContent) == 0) {
            exit;
        }

        // 获取私有邮件队列列表
        foreach ($EmailContent as $ec) {
            $company_id = $ec->company_id;
            $quere_id[] = $ec->id;

            if ($ec->ask_id > 0) {
                $prefix = '_staff'; // 获取用户接收列表
                $adminContent = DB::table($company_id . $prefix . '_quere')
                ->where($company_id . $prefix . '_quere.execute', '=', '0')
                ->whereIN($company_id . $prefix . '_quere.quere_id', $quere_id)
                ->select($company_id . $prefix . '_quere.*')
                ->orderBy($company_id . $prefix . '_quere.created_at', 'asc')->paginate();
            } else {
                $adminContent = '';
            }

            if ($ec->reply_id > 0) {
                $prefix = '_user'; // 获取客户接收列表
                $userContent = DB::table($company_id . $prefix . '_quere')
                ->where($company_id . $prefix . '_quere.execute', '=', '0')
                ->whereIN($company_id . $prefix . '_quere.quere_id', $quere_id)
                ->select($company_id . $prefix . '_quere.*')
                ->orderBy($company_id . $prefix . '_quere.created_at', 'asc')->paginate();
            } else {
                $userContent = '';
            }
        }

        if ($adminContent == '' && $userContent == '') {
            return;exit;
        } elseif ($adminContent != '') {
            // 获得用户工单标题
            foreach ($adminContent as $ac => $a) {
                $adminContent[$ac]->title = DB::table($a->company_id . '_work_order')
                ->where($a->company_id . '_work_order.id', $a->work_id)
                ->pluck($a->company_id . '_work_order.title');
            }

            // 判断用户邮件配置
            foreach ($adminContent as $ac => $a) {
                $adminContent[$ac]->to_enable = DB::table('users')->where('user_name', $a->to_email)
                ->join('site_user', 'site_user.user_id', '=', 'users.id')->pluck('site_user.enable');
            }
        } elseif ($userContent != '') {
            // 获得客户工单标题
            foreach ($userContent as $uc => $u) {
                $userContent[$uc]->title = DB::table($u->company_id . '_work_order')
                ->where($u->company_id . '_work_order.id', $u->work_id)
                ->pluck($u->company_id . '_work_order.title');
            }

            // 判断客户邮件配置
            foreach ($userContent as $uc => $u) {
                $userContent[$uc]->to_enable = DB::table('users')->where('user_name', $u->to_email)
                ->join('site_user', 'site_user.user_id', '=', 'users.id')->pluck('site_user.enable');
            }
        }

        // 邮件发送列表组合
        // 用户邮件列表组合
        if ($adminContent != '') {
            foreach ($adminContent as $kac => $ka) {
                $adminContents[$kac]['id'] = $ka->id;
                $adminContents[$kac]['company_id'] = $ka->company_id;
                $adminContents[$kac]['from_email'] = $ka->from_email;
                $adminContents[$kac]['to_email'] = $ka->to_email;
                $adminContents[$kac]['from_name'] = $ka->from_email;
                $adminContents[$kac]['to_name'] = $ka->to_email;
                $adminContents[$kac]['work_id'] = $ka->work_id;
                $adminContents[$kac]['title'] = $ka->title;
                $adminContents[$kac]['content'] = $ka->content;
                $adminContents[$kac]['created_at'] = $ka->created_at;
                $adminContents[$kac]['q_status'] = 'staff';
                $adminContents[$kac]['tpl'] = 'ask'; // 回复用户模版
                if ($ka->to_email === 0 || $ka->to_enable == 'stop') {
                    $adminContents[$kac]['url'] = 'error';
                } else {
                    $urla = URL::to($ka->path.'/admin/work/workdetail/'.$ka->work_id); // 用户接收邮件URL
                    $adminContents[$kac]['url'] = $urla;
                }
            }
        } else {
            $adminContents = 0;
        }

        // 客户邮件列表组合
        if ($userContent != '') {
            foreach ($userContent as $kuc => $ku) {
                $userContents[$kuc]['id'] = $ku->id;
                $userContents[$kuc]['company_id'] = $ku->company_id;
                $userContents[$kuc]['from_email'] = $ku->from_email;
                $userContents[$kuc]['to_email'] = $ku->to_email;
                $userContents[$kuc]['from_name'] = $ku->from_email;
                $userContents[$kuc]['to_name'] = $ku->to_email;
                $userContents[$kuc]['work_id'] = $ku->work_id;
                $userContents[$kuc]['title'] = $ku->title;
                $userContents[$kuc]['content'] = $ku->content;
                $userContents[$kuc]['created_at'] = $ku->created_at;
                $userContents[$kuc]['q_status'] = 'user';
                $userContents[$kuc]['tpl'] = 'reply'; // 回复客户模版

                if ($ku->to_email === 0 || $ku->to_enable == 'stop') {
                    $userContents[$kuc]['url'] = 'error';
                } else {
                    $urlu = URL::to($ku->path.'/site/work/workdetail/'.$ku->work_id); // 客户接收邮件URL
                    $userContents[$kuc]['url'] = $urlu;
                }
            }
        } else {
            $userContents = 0;
        }

        // 合并用户与客户邮件组合列表
        if ($userContents == 0) {
            $email = $adminContents;
        } else if ($adminContents == 0) {
            $email = $userContents;
        } else {
            $email = array_merge($adminContents, $userContents);
            foreach ($email as $key => $value) {
                $eaccuracy[$key] = $value['created_at'];
            }
            array_multisort($eaccuracy, SORT_ASC, $email);
        }

        foreach ($email as $k => $v) {
            $d = array(
                'url' => $v['url'], // 工单地址
                'work_id' => $v['work_id'], // 工单号
                'content' => $v['content'], // 工单回复内容
            );
            $u = array(
                'from_email' => $v['from_email'], // 发信人邮箱
                'from_name'  => $v['from_name'], // 发信人姓名
                'to_email' => $v['to_email'], // 收信人邮箱
                'to_name'  => $v['to_name'], // 收信人姓名
                'title' => $v['title'], // 工单标题
                'work_id' => $v['work_id'], // 工单号
            );

            // 获取表前缀
            $TablePrefix = Schema::getConnection()->getTablePrefix();
            // 公司表前缀
            $COM_TablePrefix = $TablePrefix . $v['company_id'] . '_';

            // 发送邮件处理
            if ($v['url'] == 'error') {
                // 公司私用队列状态变更
                $sql = "update " . $COM_TablePrefix . $v['q_status'] . "_quere set execute = '1' where id = " . $v['id'];
                DB::update($sql);
            } else {
                if ($v['q_status'] == 'user' && $v['tpl'] == 'reply') {
                    $mail_status = Mail::send('emails.ticket.reply', $d, function ($m) use ($u) {
                        $m->from($u['from_email'], $u['from_name']);
                        $m->to($u['to_email'], $u['to_name']);
                        $m->subject('工单#' . $u['work_id'] . ': ' . $u['title']);
                    });
                } else if ($v['q_status'] == 'staff' && $v['tpl'] == 'ask') {
                    $mail_status = Mail::send('emails.ticket.ask', $d, function ($m) use ($u) {
                        $m->from($u['from_email'], $u['from_name']);
                        $m->to($u['to_email'], $u['to_name']);
                        $m->subject('工单#' . $u['work_id'] . ': ' . $u['title']);
                    });
                }

                if ($mail_status) {
                    // 公司私用队列状态变更
                    $sql = "update " . $COM_TablePrefix . $v['q_status'] . "_quere set status = '1', 
                    execute = '1' where id = " . $v['id'];
                    DB::update($sql);

                    // 公司邮箱发放总数记录
                    if ($v['tpl'] == 'ask') {
                        $uID = DB::table('users')->where('user_name', $v['from_email'])->pluck('id');
                        $gID = DB::table($v['company_id'] . '_bind_user')->where('user_id', $uID)->pluck('group_id');
                        $eID = DB::table($v['company_id'] . '_groups')->where('id', $gID)->pluck('email_id');
                        $emailSum = DB::table('company_email')->where('id', $eID)->first();
                        $nums = $emailSum->nums;
                        $emailID = $emailSum->id;
                    } else if ($v['tpl'] == 'reply') {
                        $sumEmail = DB::table('company_email')->where('email', $v['from_email'])->first();
                        $nums = $sumEmail->nums;
                        $emailID = $sumEmail->id;
                    }

                    $sum = $nums + 1;
                    $sqlsum = "update " . $TablePrefix . "company_email set nums = " . $sum . " where id = " . $emailID;
                    DB::update($sqlsum);
                } else {
                    // 公司私用队列状态变更
                    $sql = "update " . $COM_TablePrefix . $v['q_status'] . "_quere set execute = '1' where id = " . $v['id'];
                    DB::update($sql);
                }
            }
        }

        // 公用队列状态变更
        if (is_array($quere_id)) {
            $qId = implode(',', $quere_id);
        }
        $sqls = "update " . $TablePrefix . "quere set execute = '1' where id in ( " . $qId . ")";
        DB::update($sqls);

        return;exit;
    }

    // 工单邮件统计发送脚本处理
    public function countQuere()
    {
        echo '工单邮件统计发送脚本处理';
        exit;
    }

    // binway需求 - start
    // 工单邮件发送脚本
    // http://localhost/ecdesk/public/api/v1/emailrequest?byname=Xab7mo7E
    public function emailRequest()
    {
        // 公司别名
        $byname = $_GET['byname'];

        // 获取公司ID
        $company_id = DB::table('companys')->where('byname', '=', $byname)->pluck('id');

        // 获取未发送的邮件队列
        $data = DB::table($company_id . '_order_quere')->where('company_id', '=', $company_id)
        ->where('process', '=', 0)->where('execute', '=', '0')->get();
        
        // 邮件队列发送处理
        foreach ($data as $k => $v) {
            $d = array(
                'content' => $v->content, // 回复内容
            );
            $u = array(
                'from_email' => $v->from_email, // 发信人邮箱
                'to_email' => $v->to_email, // 收信人邮箱
                'title' => $v->title, // 工单标题
                'cc' => $v->cc, // 抄送对象
                'bcc' => $v->bcc, // 暗送对象
                'attach' => $v->enclosure, // 附件对象
            );

            // 发送邮件
            $mail_status = Mail::send('emails.ticket.email', $d, function ($m) use ($u) {
                // 发件人
                $m->from($u['from_email']);

                // 判断是否有抄送或暗送对象
                if ($u['cc'] != '' || $u['bcc'] != '') {
                    // 按逗号分割成数组
                    if ($u['cc'] != '') {
                        $cc_data = explode(',', $u['cc']);
                        if (count($cc_data) == 1) {
                            $cc_data = $cc_data[0];
                        }
                    } else {
                        $cc_data = '';
                    }

                    if ($u['bcc'] != '') {
                        $bcc_data = explode(',', $u['bcc']);
                        if (count($bcc_data) == 1) {
                            $bcc_data = $bcc_data[0];
                        }
                    } else {
                        $bcc_data = '';
                    }

                    if ($cc_data != '' && $bcc_data != '') {
                        $m->to($u['to_email'])->cc($cc_data)->bcc($bcc_data);
                    } elseif ($cc_data != '' && $bcc_data == '') {
                        $m->to($u['to_email'])->cc($cc_data);
                    } elseif ($cc_data == '' && $bcc_data != '') {
                        $m->to($u['to_email'])->bcc($bcc_data);
                    }
                } else {
                    $m->to($u['to_email']);
                }

                // 判断是否有附件对象
                if ($u['attach'] != '') {
                    // 按逗号分割成数组
                    $attach_data = explode(',', $u['attach']);

                    // 获取上传附件路径
                    $attach_path = DB::table('enclosures')->whereIn('id', $attach_data)->get();
                    $pathToFile = '';
                    if (count($attach_path) == 1) {
                        // 单个取首元素抄送附件
                        $pathToFile = $m->attach($attach_path[0]->path);
                    } else {
                        foreach ($attach_path as $k => $v) {
                            // 多个组合抄送附件
                            $pathToFile = $m->attach($v->path);
                        }
                    }

                    $m = $pathToFile;
                }

                // 邮件标题
                $m->subject($u['title']);
            });

            // 邮件发送后执行队列状态变更
            $updated_at = date('Y-m-d H:i:s', time());
            if ($mail_status) {
                DB::table($company_id . '_order_quere')->where('id', $v->id)
                ->update(array('execute' => '1', 'system' => 1, 'updated_at' => $updated_at));
            } else {
                DB::table($company_id . '_order_quere')->where('id', $v->id)
                ->update(array('execute' => '1', 'system' => 2, 'updated_at' => $updated_at));
            }
        }

        return;exit;
    }

    // 工单邮件接收脚本
    // http://localhost/ecdesk/public/api/v1/emailresponse?byname=Xab7mo7E
    public function emailResponse()
    {
        // 公司别名
        $byname = $_GET['byname'];

        // 获取公司ID
        $company_id = DB::table('companys')->where('byname', '=', $byname)->pluck('id');
        if ($company_id) {
            // 获取公司邮件列表
            $data = DB::table('company_email')->where('company_id', '=', $company_id)
            ->where('disabled', '=', 'false')->orderBy('validation_time', 'asc')->first();
            if (count($data) > 0) {
                // 创建邮件接口对象
                $bec = new BaseEmailController();

                // 打开mailbox
                $mailbox = '{'.$data->host.':'.$data->port.'}'; // 组合参数
                $bec->openMailBox($mailbox, $data->email, $data->pass);

                // 指定邮箱目录
                $inbox_path = array('inbox'=>$data->inbox_path);
                $bec->setBoxPath($inbox_path);

                // 进入收件箱
                $bec->openInbox();

                // 获取邮箱的邮件uid信息并过滤
                $request = $bec->fetchUids();
                if (empty($request)) {
                    $request = array();
                }

                if (count($request) > 0) {
                    foreach ($request as $k => $v) {
                        if (DB::table($company_id . '_order_ask')->where('uid', '=' , $v)
                            ->where('style', '=', 'inbox')->where('email', '=' , $data->id)->pluck('uid')) {
                            unset($request[$k]);
                        }
                    }

                    // 设定需拉取的uid数量
                    if (count($request) > $this->page) {
                        $sum = 1;
                        foreach ($request as $k => $v) {
                            if ($sum <= $this->page) {
                                $uids[$k] = $v;
                                $sum ++;
                            }
                        }
                    } else {
                        $uids = $request;
                    }
                }

                // 根据uid获取指定邮件内容生成工单作业
                if (count($request) > 0) {
                    $response = $bec->fetchMailsByUids($uids);
                    if (count($response) > 0) {
                        $this->emailOrder($response, $company_id, 'inbox', $data->id);
                    }
                }

                // 关闭mailbox
                $bec->closeMailBox();
                unset($bec);
                $bec = null;
                
                // 记录当前邮箱拉取时间标识
                DB::table('company_email')->where('id', '=', $data->id)->update(array('validation_time' => time()));
            }
        }

        return;exit;
    }

    // 垃圾邮件接收脚本
    // http://localhost/ecdesk/public/api/v1/emailspamresponse?byname=Xab7mo7E
    public function emailSpamResponse()
    {
        // 公司别名
        $byname = $_GET['byname'];

        // 获取公司ID
        $company_id = DB::table('companys')->where('byname', '=', $byname)->pluck('id');
        if ($company_id) {
            // 获取公司邮件列表
            $data = DB::table('company_email')->where('company_id', '=', $company_id)
            ->where('disabled', '=', 'false')->orderBy('validation_time', 'asc')->first();
            if (count($data) > 0) {
                // 创建邮件接口对象
                $bec = new BaseEmailController();

                // 打开mailbox
                $mailbox = '{'.$data->host.':'.$data->port.'}'; // 组合参数
                $bec->openMailBox($mailbox, $data->email, $data->pass);

                // 指定邮箱目录
                $spam_path = array('spam'=>$data->spam_path);
                $bec->setBoxPath($spam_path);

                // 进入垃圾箱
                $bec->openSpambox();

                // 获取垃圾箱的邮件uid信息并过滤
                $request = $bec->fetchUids();
                if (empty($request)) {
                    $request = array();
                }
                
                if (count($request) > 0) {
                    foreach ($request as $k => $v) {
                        if (DB::table($company_id . '_order_ask')->where('uid', '=' , $v)
                            ->where('style', '=', 'spam')->where('email', '=' , $data->id)->pluck('uid')) {
                            unset($request[$k]);
                        }
                    }

                    // 设定需拉取的uid数量
                    if (count($request) > $this->page) {
                        $sum = 1;
                        foreach ($request as $k => $v) {
                            if ($sum <= $this->page) {
                                $uids[$k] = $v;
                                $sum ++;
                            }
                        }
                    } else {
                        $uids = $request;
                    }
                }

                // 根据uid获取指定垃圾箱邮件内容生成工单作业
                if (count($request) > 0) {
                    $response = $bec->fetchMailsByUids($uids);
                    if (count($response) > 0) {
                        $this->emailOrder($response, $company_id, 'spam', $data->id);
                    }
                }

                // 关闭mailbox
                $bec->closeMailBox();
                unset($bec);
                $bec = null;

                // 记录当前邮箱拉取时间标识
                DB::table('company_email')->where('id', '=', $data->id)->update(array('validation_time' => time()));
            }
        }

        return;exit;
    }

    // 工单邮件附件接收脚本
    // http://localhost/ecdesk/public/api/v1/emailattachment?byname=Xab7mo7E
    public function emailAttachment()
    {
        // 公司别名
        $byname = $_GET['byname'];

        // 获取公司ID
        $company_id = DB::table('companys')->where('byname', '=', $byname)->pluck('id');
        if ($company_id) {
            // 获取收件箱或垃圾箱需要拉取附件的工单
            $mail = DB::table($company_id . '_order_ask')
            ->join('company_email', 'company_email.id', '=', $company_id . '_order_ask.email')
            ->where($company_id . '_order_ask.attachment', '=', 1)
            ->where($company_id . '_order_ask.style', '!=', 'order')
            ->where('company_email.disabled', '=', 'false')
            ->select('company_email.host', 'company_email.port', 'company_email.pass', 
            'company_email.email as username', $company_id . '_order_ask.*')->get();
            if (count($mail) > 0) {
                // 循环下载附件列表
                foreach ($mail as $k => $v) {
                    // 创建邮件接口对象
                    $bec = new BaseEmailController();

                    // 打开mailbox
                    $mailbox = '{'.$v->host.':'.$v->port.'}'.$v->style; // 组合参数
                    $bec->openMailBox($mailbox, $v->username, $v->pass);

                    // 邮件附件uid有效期判断
                    $msgnos = array();
                    $msgnos = $bec->AttachmentMailsByUid($v->uid);
                    if ($msgnos[0] == 0) {
                        DB::table($company_id . '_order_ask')->where('id', $v->id)->update(array('attachment' => 3));
                    } else {
                        // 获取收件箱或垃圾箱邮件附件
                        $savedirpath = 'uploads/' . $byname . '/ask/' . date('Y/m/d/') . $v->ask; // 附件储存路径
                        $bec->emailAttachment($savedirpath, $v->msgno, $v->uid, $v->id, $v->order, $v->ask, $company_id);
                    }

                    // 关闭mailbox
                    $bec->closeMailBox();
                    unset($bec);
                    $bec = null;
                }
            }
        }
        
        return;exit;
    }

    // 收取邮件创建工单
    public function emailOrder($data, $company_id, $style, $email)
    {
        foreach ($data as $k => $v) {
            // 判断新老客户
            $from = $this->paypalalibabaMessage($v->from, $v->subject);
            if ($this->createUser($from, $v->fromaddress, $company_id, $email) == 0) {
                // 创建工单
                $this->createOrder($from, $v->ccaddress, $v->subject, $v->body, $v->msgno, $company_id, $style, $email, $v->attachment, $v->udate, $v->uid, $v->type);
            }
        }
    }

    // 过滤paypal阿里巴巴通知邮件邮箱用户名
    public function paypalalibabaMessage($from, $title)
    {
        $data = '';
        $from_name = explode("@", $from);

        // paypal过滤
        if ($from_name[0] == 'member' && $from_name[1] == 'paypal.com' && substr($title, -1) == ')') {
            $title_head = explode("(", $title);
            $title_end = rtrim($title_head[1], ")");
            if (strstr($title_end, "@")) {
                $data = $title_end;
            } else {
                $data = $from;
            }
        } elseif ($from_name[0] == 'feedback' && $from_name[1] == 'service.alibaba.com') { // 阿里巴巴过滤
            if (strpos($title, '[') !== false && strpos($title, ']') !== false) {
                $title_head = explode("[", $title);
                $title_body = explode("]", $title_head[1]);
                $title_end = $title_body[0];
                if (strstr($title_end, "@")) {
                    $data = $title_end;
                } else {
                    $data = $from;
                }
            } else {
                $data = $from;
            }
        } else {
            $data = $from;
        }

        return $data;
    }

    // 新用户创建
    public function createUser($from, $fromaddress, $company_id, $email)
    {
        $config = 0;
        $id = DB::table('users')->where('user_name', $from)->pluck('id');
        $uid = DB::table('site_user')->where('user_id', $id)->where('company_id', $company_id)->pluck('user_id');
        if ($uid) {
            $config = 0;
        } else {
            // 创建客户ID
            if (!$id) {
                // 初始化客户模型类
                $user = new User;

                // 组合客户数据
                $password = '111111';
                $user->password  = Hash::make($password);
                $user->user_name = $from;
                $user->mobile    = '11111111111';
                $user->head      = 0;
                $user->disabled  = 'false';
                if ($from == $fromaddress) {
                    $from_name = explode("@", $from);
                    $user->name = $from_name[0];
                } else {
                    $user->name = $fromaddress;
                }

                // 保存客户数据
                if ($user->save()) {
                    $user_id = $user->id;
                } else {
                    $user_id = 0;
                }
            } else {
                $user_id = $id;
            }

            // 创建客户绑定关系
            if ($user_id > 0) {
                // 初始化客户关系模型
                $site_user = new SiteUser();

                // 客户关系数据
                $site_user->company_id   = $company_id;
                $site_user->user_id      = $user_id;
                $site_user->work_type_id = 1;
                $site_user->disabled     = 'false';
                $site_user->firm = 'email';

                // 客户关系保存
                if ($site_user->save()) {
                    // 客户组别绑定
                    $created_at = date('Y-m-d H:i:s', time());
                    $updated_at = date('Y-m-d H:i:s', time());
                    $group = DB::table($company_id . '_groups')->where('email_id', $email)->pluck('id');
                    $status = DB::table($company_id . '_bind_user')->insert(
                        array('user_id'=>$user_id, 'group_id'=>$group, 'admin_id'=>0, 
                        'created_at'=>$created_at, 'updated_at'=>$updated_at)
                    );

                    if ($status) {
                        $config = 0;
                    } else {
                        $config = 1;
                    }
                } else {
                    $config = 1;
                }
            } else {
                $config = 1;
            }
        }

        return $config;
    }

    // 工单创建
    public function createOrder($from, $ccaddress, $title, $content, $msgno, $company, $style, $email, $attachment, $udate, $uid, $type)
    {
        $config = 0;
        $user_id = DB::table('users')->where('user_name', $from)->pluck('id');
        // $group = DB::table($company . '_bind_user')->where('user_id', $user_id)->pluck('group_id');
        $group = DB::table($company . '_groups')->where('email_id', $email)->pluck('id');
        $created_at = date('Y-m-d H:i:s', $udate);
        $updated_at = date('Y-m-d H:i:s', $udate);

        // 截取标题获得指定工单号
        if ($style == 'inbox' && strstr($title, "#")) {
            $title_s = explode("#", $title);
            $title_e = explode(":", $title_s[1]);
            $order_title_id = $title_e[0];

            // 判断是否是老工单
            $order_old_id = DB::table($company . '_order')->where('id', $order_title_id)->pluck('id');
            if ($order_old_id) {
                // binway需求老工单一律去除原处理人员进工单池等待重新分配 - start
                DB::table($company . '_order')->where('id', '=', $order_old_id)->update(array('status' => 0, 'reply' => 0, 'updated_at'=>$updated_at));
                // binway需求老工单一律去除原处理人员进工单池等待重新分配 - end
                // DB::table($company . '_order')->where('id', '=', $order_old_id)->update(array('status' => 1));
                $order_id = $order_old_id;
            } else {
                $order_id = 0;
            }
        } else {
            $order_id = 0;
        }

        // 判断是否为新工单创建
        if ($order_id == 0) {
            if ($style == 'inbox') {
                $status = DB::table($company . '_order')->insert(
                    array('title'=>$title, 'group'=>$group, 'ask'=>$user_id, 'email'=>$email, 'created_at'=>$created_at, 'updated_at'=>$updated_at)
                );
            } elseif ($style == 'spam') {
                $status = DB::table($company . '_order')->insert(
                    array('title'=>$title, 'group'=>$group, 'ask'=>$user_id, 'trash'=>1, 'email'=>$email, 'created_at'=>$created_at, 'updated_at'=>$updated_at)
                );
            }
            
            if ($status) {
                // 获取最新工单ID
                $order_id = DB::table($company . '_order')->max('id');
                $config = 0;
            } else {
                $config = 1;
            }
        }

        // 创建工单记录
        if ($config == 0) {
            // 将换行符转换成<br />
            // $content = str_replace("\r\n", "<br>", $content);
            if ($type != 'html') {
                $content = str_replace("\r\n", "<br>", $content);
            }

            if ($style == 'inbox') {
                DB::table($company . '_order_ask')->insert(
                    array('order'=>$order_id, 'ask'=>$user_id, 'content'=>$content, 'msgno'=>$msgno, 'cc'=>$ccaddress, 
                    'style'=>'inbox', 'email'=>$email, 'attachment'=>$attachment, 'udate'=>$udate, 'uid'=>$uid, 'created_at'=>$created_at, 'updated_at'=>$updated_at)
                );
            } elseif ($style == 'spam') {
                DB::table($company . '_order_ask')->insert(
                    array('order'=>$order_id, 'ask'=>$user_id, 'content'=>$content, 'msgno'=>$msgno, 'cc'=>$ccaddress, 
                    'style'=>'spam', 'email'=>$email, 'attachment'=>$attachment, 'udate'=>$udate, 'uid'=>$uid, 'created_at'=>$created_at, 'updated_at'=>$updated_at)
                );
            }
        }
    }

    // ecstore会员同步接口
    // http://cs.heavengifts.com/api/v1/user/?byname=DswyTp91&page_size=5000&page_no=1
    public function emailUser()
    {
        // 获取参数
        $byname = $_GET['byname'];
        $page_size = $_GET['page_size'];
        $page_no = $_GET['page_no'];

        // 接口地址
        $direct = '?direct=true';
        $method = '&method=b2c.member.basic.get_member_filter';
        $data = '&data='.date('Y-m-d H:i:s');
        $page = '&page_size='.$page_size;
        $limit = '&page_no='.$page_no;
        $url = 'http://heavengifts.com/api'.$direct.$method.$data.$page.$limit;

        // 请求接口并获得返回结果
        $request = $this->curlPost($url);
        // 微信返回信息解码
        $response = json_decode($request);
        if ($response->rsp == 'succ') {
            $data = $response->data;
            if (count($data) == 0) {
                exit;
            }

            // 获取公司ID
            $com_id = DB::table('companys')->where('byname', '=', $byname)->pluck('id');
            if ($com_id) {
                foreach ($data as $k => $v) {
                    if (DB::table('users')->where('user_name', '=', $v->email)->pluck('id')) {
                        // error_log(var_export($v->email,1),3,dirname(__FILE__).'/user.log');
                        unset($data[$k]);
                    } else {
                        // 创建会员
                        $area = explode(":", $v->area);
                        if (isset($area[1])) {
                            $country = $area[1];
                        } else {
                            $country = '';
                        }
                        
                        $user = $v->login_name;
                        $remark = $v->remark;
                        $user_name = $v->email;
                        if ($v->name == '') {
                            $name = $v->login_name;
                        } else {
                            $name = $v->name;
                        }

                        // 初始化客户模型类
                        $user = new User;

                        // 组合客户数据
                        $password = '111111';
                        $user->password  = Hash::make($password);
                        $user->user_name = $user_name;
                        $user->mobile    = '11111111111';
                        $user->head      = 0;
                        $user->disabled  = 'false';
                        $user->name = $name;

                        // 保存客户数据
                        if ($user->save()) {
                            $user_id = $user->id;

                            // 初始化客户关系模型
                            $site_user = new SiteUser();

                            // 客户关系数据
                            $site_user->company_id   = $com_id;
                            $site_user->user_id      = $user_id;
                            $site_user->work_type_id = 1;
                            $site_user->disabled     = 'false';
                            $site_user->firm = 'email';
                            $site_user->country = $country;
                            $site_user->user = $user;
                            $site_user->remark = $remark;

                            // 客户关系保存
                            if ($site_user->save()) {
                                // 客户组别绑定
                                $created_at = date('Y-m-d H:i:s');
                                $updated_at = date('Y-m-d H:i:s');
                                DB::table($com_id . '_bind_user')->insert(
                                array('user_id'=>$user_id, 'group_id'=>1, 'admin_id'=>0, 
                                'created_at'=>$created_at, 'updated_at'=>$updated_at));
                            }
                        }
                    }
                }
            } else {
                return '无公司ID';
            }
        } else {
            return '无返回数据';
        }

        return $user_id;
    }

    public function curlPost($url, $data = NULL)
    {
        // curl初始化
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // 请求执行并获取返回结果
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        // 释放curl句柄
        curl_close($ch);

        return $tmpInfo;
    }
    // binway需求 - end
}
