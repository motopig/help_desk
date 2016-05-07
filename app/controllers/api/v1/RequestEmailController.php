<?php

class RequestEmailController extends Controller 
{
    // 工单邮件内容读取脚本处理
    // 脚本地址：http://localhost/ecdesk/public/api/v1/imapwork
    // 每2分钟执行命令：*/2 * * * * /usr/bin/curl http://localhost/ecdesk/public/api/v1/imapwork
    public function imapWork()
    {
        // 获得最早标记时间记录的已启用邮件
        $emailOne = CompanyEmail::orderBy('validation_time', 'asc')->where('disabled', '=', 'false')->where('validation_time', '>', 0)->first();
        if (count($emailOne) == 0) {
            exit;
        } else {
            // 创建邮件接口对象
            $mailList = array();
            $mail = new EvebitEmailController();
        }

        // 正式使用 - start
        // 配置信息
        $host = $emailOne->host; //邮箱服务主机
        $port = $emailOne->port; //imap连接地址：不同主机地址不同
        $user = $emailOne->email; //邮箱用户名
        $pass = $emailOne->pass; //邮箱密码
        $company = $emailOne->company_id; //公司ID

        // 备份最新邮件读取标记时间
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $cID = $emailOne->id; // 公司邮箱ID
        $vsql = "update " . $TablePrefix . "company_email set validation_time = " . time() . " where id = " . $cID;
        $validation = DB::update($vsql);

        // 邮件生成工单处理流程
        if ($validation) {
            // 循环读取邮件信息
            $mailList = $this->requestEmail($host, $port, $user, $pass, $mail);
            if (is_array($mailList) && !empty($mailList)) {
                // 过滤已生成工单记录的邮件
                foreach ($mailList as $mk => $mv) {
                    if (DB::table($company . '_work_ask')->where('msgno', '=' , $mv['id'])->pluck('msgno')) {
                        unset($mailList[$mk]);
                    }
                }

                // 循环邮件生成工单
                if (isset($mailList)) {
                    foreach ($mailList as $mk => $mv) {
                        $this->emailWork($mv['id'], $mv['from'], $mv['subject'], $mv['body'], '', $company);
                    }
                }
            }
        }
        
        // 关闭邮件端口
        $mail->closeMail();
        return;exit;
        // 正式使用 - end

        // // 测试使用 - start
        // $host = 'imap.163.com'; //邮箱服务主机
        // $port = '143'; //imap连接地址：不同主机地址不同
        // $user = ''; //邮箱用户名
        // $pass = ''; //邮箱密码
        // $company = '1'; //公司ID
        // $mailList = $this->requestEmail($host, $port, $user, $pass, $mail);
        // // 过滤已生成工单记录的邮件
        // foreach ($mailList as $mk => $mv) {
        //     if (DB::table($company . '_work_ask')->where('msgno', $mv['id'])->pluck('msgno')) {
        //         unset($mailList[$mk]);
        //     }
        // }

        // // 循环邮件生成工单
        // if ($mailList != 0) {
        //     foreach ($mailList as $k => $v) {
        //         $this->emailWork($v['id'], $v['from'], $v['subject'], $v['body'], $v['Answered'], $company);
        //     }
        // }
        
        // // 关闭邮件端口
        // $mail->closeMail();
        // return;exit;
        // // 测试使用 - end
    }

    // 接口拉取邮件处理
    public function requestEmail($host = NULL, $port = NULL, $user = NULL, $pass = NULL, $mail = NULL)
    {
        $connect = $mail->imapMail($host, $port, $user, $pass); // 连接邮箱服务器
        if ($connect) {
            // 获得当前日期前一天的邮箱收件总数
            $date = date("d M Y", time());
            $totalCount = imap_search($connect, "ON \"$date\"");
            
            // 获得邮件头部及正文内容
            if ($totalCount) {
                foreach ($totalCount as $k => $v) {
                    $mailList[] = $mail->mailHeader($v);
                }

                sort($mailList); // 按最早时间排序
            } else {
                $mailList = 0;
            }
        }
        
        return $mailList;
    }

    // 邮件生成工单处理
    public function emailWork($msgno = NULL, $from = NULL, $subject = NULL, $body = NULL, $answered = NULL, $company = NULL)
    {
        $uIDs = DB::table('users')->where('user_name', $from)->pluck('id');
        $uID = DB::table('site_user')->where('user_id', $uIDs)->pluck('user_id');
        if ($uID) {
            $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
            $COM_TablePrefix = $TablePrefix . $company . '_'; // 公司表前缀
            $group_id = DB::table($company . '_bind_user')->where('user_id', $uID)->pluck('group_id'); // 获得组别
            $admin_id = DB::table($company . '_bind_user')->where('user_id', $uID)->pluck('admin_id'); // 获得绑定组员
            $created_at = date('Y-m-d H:i:s', time()); // 工单创建时间
            $updated_at = date('Y-m-d H:i:s', time()); // 工单修改时间
            $enclosure_id = 0; // 暂时设置邮件工单无附件
            $ask_anchor = '';
            $system = 0;

            if (strstr($subject, "工单#")) {
                // 截取标题获得指定工单号
                $subject_t = explode("#", $subject);
                $subject_z = explode(":", $subject_t[1]);
                $subject_w = $subject_z[0];

                if ($wID = DB::table($company . '_work_order')->where('work_email', 'true')->where('id', $subject_w)->whereNotIn('work_status',  array('1', '4'))->pluck('id')) {
                    $work_order_id = $wID; // 原工单ID

                    // binway需求：已完成工单，客户重新发送邮件；工单状态设置为处理中 - start
                    $work_status = '2';
                    $sql = "update " . $COM_TablePrefix . "work_order set work_status = '2' where id = " . $work_order_id;
                    DB::update($sql);
                    // binway需求：已完成工单，客户重新发送邮件；工单状态设置为处理中 - end

                    $ask = str_replace("\r\n", "<br>", $body); // 将换行符转换成<br />
                    $aStatus = DB::insert('insert into ' . $COM_TablePrefix . 'work_ask (work_order_id, user_id, ask, enclosure_id, 
                    ask_anchor, system, created_at, updated_at, msgno) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    array($work_order_id, $uID, $ask, $enclosure_id, $ask_anchor, $system, $created_at, $updated_at, $msgno));
                }
            } else {
                // 获得业务类型
                $work_type_id = DB::table('site_user')->where('user_id', $uID)->pluck('work_type_id');
                $work_level = '1'; // 工单级别：低
                $work_email = 'true'; // 是否邮件生成工单
                
                // 创建工单
                $wStatus = DB::insert('insert into ' . $COM_TablePrefix . 'work_order (user_id, group_id, admin_id, 
                work_type_id, work_level, work_email, title, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                array($uID, $group_id, $admin_id, $work_type_id, $work_level, $work_email, $subject, $created_at, $updated_at));

                if ($wStatus) {
                    $result = DB::select('select max(id) as id from ' . $COM_TablePrefix . 'work_order');
                    $work_order_id = $result[0]->id; // 最新工单ID
                    $ask = str_replace("\r\n", "<br>", $body); // 将换行符转换成<br />
                    $aStatus = DB::insert('insert into ' . $COM_TablePrefix . 'work_ask (work_order_id, user_id, ask, enclosure_id, 
                    ask_anchor, system, created_at, updated_at, msgno) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    array($work_order_id, $uID, $ask, $enclosure_id, $ask_anchor, $system, $created_at, $updated_at, $msgno));
                }
            }

            if ($aStatus) {
                $results = DB::select('select max(id) as id from ' . $COM_TablePrefix . 'work_ask');
                $path = DB::table('companys')->where('id', $company)->pluck('path');
                $this->quereUser($company, $from, $results[0]->id, $admin_id, $group_id, $ask, $path, $work_order_id);
            }
        } else { // binway需求：不存在平台内的邮件客户，自动创建新账户
            $uID = $this->newEmailUser($from, $company);
            if ($uID > 0) {
                $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
                $COM_TablePrefix = $TablePrefix . $company . '_'; // 公司表前缀
                $group_id = DB::table($company . '_bind_user')->where('user_id', $uID)->pluck('group_id'); // 获得组别
                $admin_id = DB::table($company . '_bind_user')->where('user_id', $uID)->pluck('admin_id'); // 获得绑定组员
                $created_at = date('Y-m-d H:i:s', time()); // 工单创建时间
                $updated_at = date('Y-m-d H:i:s', time()); // 工单修改时间
                $enclosure_id = 0; // 暂时设置邮件工单无附件
                $ask_anchor = '';
                $system = 0;

                // 获得业务类型
                $work_type_id = DB::table('site_user')->where('user_id', $uID)->pluck('work_type_id');
                $work_level = '1'; // 工单级别：低
                $work_email = 'true'; // 是否邮件生成工单
                
                // 创建工单
                $wStatus = DB::insert('insert into ' . $COM_TablePrefix . 'work_order (user_id, group_id, admin_id, 
                work_type_id, work_level, work_email, title, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                array($uID, $group_id, $admin_id, $work_type_id, $work_level, $work_email, $subject, $created_at, $updated_at));

                if ($wStatus) {
                    $result = DB::select('select max(id) as id from ' . $COM_TablePrefix . 'work_order');
                    $work_order_id = $result[0]->id; // 最新工单ID
                    $ask = str_replace("\r\n", "<br>", $body); // 将换行符转换成<br />
                    $aStatus = DB::insert('insert into ' . $COM_TablePrefix . 'work_ask (work_order_id, user_id, ask, enclosure_id, 
                    ask_anchor, system, created_at, updated_at, msgno) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    array($work_order_id, $uID, $ask, $enclosure_id, $ask_anchor, $system, $created_at, $updated_at, $msgno));
                }

                if ($aStatus) {
                    $results = DB::select('select max(id) as id from ' . $COM_TablePrefix . 'work_ask');
                    $path = DB::table('companys')->where('id', $company)->pluck('path');
                    $this->quereUser($company, $from, $results[0]->id, $admin_id, $group_id, $ask, $path, $work_order_id);
                }
            }
        }
    }

    // 接收邮件新用户创建
    function newEmailUser($from, $company)
    {
        $u = User::where('user_name', $from)->first();
        if (!$u) {
            // 初始化用户模型类
            $user = new User;
            // 用户数据
            $password = '111111';
            $from_name = explode("@", $from);
            $name = $from_name[0];
            $user->user_name = $from;
            $user->password  = Hash::make($password);
            $user->name      = $name;
            $user->mobile    = '11111111111';
            $user->head      = 0;
            $user->disabled  = 'false';
            // 保存用户数据
            if ($user->save()) {
                $user_id = $user->id;
            } else {
                $user_id = 0;
            }
        } else {
            $user_id = $u->id;
        }

        // 判断用户信息
        if ($user_id > 0) {
            // 初始化客户关系模型
            $site_user = new SiteUser();

            // 客户关系数据
            $site_user->company_id   = $company;
            $site_user->user_id      = $user_id;
            $site_user->work_type_id = 1;
            $site_user->disabled     = 'false';
            $site_user->firm = 'email';

            // 客户关系保存
            if ($site_user->save()) {
                // 客户组别绑定
                $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
                $COM_TablePrefix = $TablePrefix . $company . '_'; // 公司表前缀
                $created_at = date('Y-m-d H:i:s', time());
                $updated_at = date('Y-m-d H:i:s', time());
                $bStatus = DB::insert('insert into ' . $COM_TablePrefix . 'bind_user (user_id, group_id, admin_id, created_at, updated_at) 
                    values (?, ?, ?, ?, ?)', array($user_id, 1, 0, $created_at, $updated_at));

                if ($bStatus) {
                    return $user_id;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return $user_id;
        }
    }

    // 客户邮件发起内容接收邮件队列
    public function quereUser($company = NULL, $from = NULL, $ask_id = NULL, $admin_id = NULL, $group_id = NULL, $content = NULL, $path = NULL, $work_id = NULL)
    {
        // 接受人邮箱，无绑定组员则选择组别邮箱
        $status = 'true'; // 队列开关
        // if ($admin_id == 0) {
        //     $email_id = DB::table($company . '_groups')->where('id', $group_id)->pluck('email_id');
        //     $level = 0;

        //     if ($email_id > 0) {
        //         $to_email = DB::table('company_email')->where('id', $email_id)->pluck('email');
        //         if (!$to_email)
        //             $status = 'false';
        //     } else {
        //         // $to_email = $email_id;
        //         $status = 'false';
        //     }
        // } else {
        //     // 查询接收人邮箱是否开启接收功能
        //     $enable = DB::table('admin_user')->where('user_id', $admin_id)->pluck('enable');
        //     if ($enable == 'stop') {
        //         $status = 'false';
        //     } else {
        //         $to_email = DB::table('users')->where('id', $admin_id)->pluck('user_name');
        //         $level = DB::table('admin_user')->where('id', $admin_id)->pluck('level');
        //     }
        // }

        // 暂时先只有客户和组员绑定的才发邮件，解决企业邮箱接收和发送邮件冲突问题
        if ($admin_id != 0) {
            // 查询接收人邮箱是否开启接收功能
            $enable = DB::table('admin_user')->where('user_id', $admin_id)->pluck('enable');
            if ($enable == 'stop') {
                $status = 'false';
            } else {
                $to_email = DB::table('users')->where('id', $admin_id)->pluck('user_name');
                $level = DB::table('admin_user')->where('id', $admin_id)->pluck('level');
            }
        } else {
            $status = 'false';
        }

        if ($status != 'false') {
            // 判断用户等级获取用户对象
            if ($level == 0 || $level == 3 || $level == 4) {
                $from_email = $from; // 发起人邮箱
                // 公用矩阵邮件队列处理
                $quere = new Quere;
                $quere->company_id = $company;
                $quere->from_email = $from_email;
                $quere->to_email = $to_email;
                $quere->ask_id = $ask_id;

                // 插入私有公司邮件队列
                if ($quere->save()) {
                    // 私有公司邮件队列处理
                    $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
                    $COM_TablePrefix = $TablePrefix . $company . '_'; // 公司表前缀
                    $created_at = date('Y-m-d H:i:s', time());
                    $updated_at = date('Y-m-d H:i:s', time());
                    $queres = DB::select('select max(id) as id from ' . $TablePrefix . 'quere');
                    DB::insert('insert into ' . $COM_TablePrefix . 'staff_quere (quere_id, company_id, from_email, to_email, 
                    ask_id, content, path, work_id, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    array($queres[0]->id, $company, $from_email, $to_email, $ask_id, $content, $path, $work_id, $created_at, $updated_at));
                }
            }
        }
    }
}
