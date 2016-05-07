<?php

// binway工单定制 - no
// 工单状态：status：0:待分配(工单池)，1:待处理，2:已处理
// 工单类型：mark：标记内容，tab：0:未标记，1:已标记，trash：0:非垃圾，1:垃圾
// 工单审核(order_reply)：system：0:无需审核或审核通过，1:待审核，2:审核不通过(驳回)
// 工单提醒(order_message)：execute：0:待提醒，1:已提醒
// 工单邮件(order_quere)：execute：0:待发送，1:已发送
// 工单客户附件(order_ask)：attachment：0:无附件，1:待拉取，2:拉取成功，3:拉取失败
// 银行凭证：bank：0:不移动到银行凭证列表，1:移动到银行凭证列表
class ComAdminOrderController extends ComAdminController
{
	// 设置分页数和查询数
    private $take = 30;
    private $skip = 0;
    private $pagecount = 0;

	// 构造方法
    public function __construct()
    {
        parent::__construct();
    }

    // 工单首页
    public function index($com)
    {
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取已启用的邮箱
        $email = DB::table('company_email')->where('company_id', '=', $com->id)->where('disabled', '=', 'false')->get();
        if (count($email) == 0) {
            return View::make('com/admin/order/email')->with('com', $com);
        }

        // 工单总数
        $count = $this->orderCount($com, $level);

        // 工单标题
        if (isset($_GET['status'])) {
            // 是否包含搜索
            if (isset($_GET['search_type'])) {
                $title = $this->orderTitle($com, $level, $_GET['status'], $_GET['search_type'], $_GET['search_content']);
            } else {
                $title = $this->orderTitle($com, $level, $_GET['status'], 0, 0);
            }

            $os = $_GET['status'];
        } else {
            // 是否包含搜索
            if (isset($_GET['search_type'])) {
                $title = $this->orderTitle($com, $level, 0, $_GET['search_type'], $_GET['search_content']);
            } else {
                $title = $this->orderTitle($com, $level, 0, 0, 0);
            }

            $os = 0;
        }

        // 搜索条目总数
        if (isset($_GET['search_type'])) {
            // 判断搜索条目是否带分页
            if (isset($_GET['search_count'])) {
                $this->pagecount = $_GET['search_count'];
            }

            $search_count = $this->pagecount;
            $search_type = $_GET['search_type'];
            $search_content = $_GET['search_content'];
        }

    	return View::make('com/admin/order/index')->with('com', $com)->with(compact('os'))
        ->with(compact('count'))->with(compact('title'))->with(compact('level'))
        ->with(compact('search_count'))->with(compact('search_type'))->with(compact('search_content'));
    }

    // 工单总数
    public function orderCount($com, $level)
    {
        // 根据用户等级获取总数
        if ($level == 4) {
            // 个人待分配工单总数
            $count['0'] = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('status', '=', 0)->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 个人待处理工单总数
            $count['1'] = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('status', '=', 1)->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 个人已处理工单总数
            $count['2'] = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('status', '=', 2)->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 个人审核池工单总数
            // 去除已扔进废纸篓的工单号
            $order_ds = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('disabled', '=', 'true')->lists('id');
            $order_rs = DB::table($com->id . '_order_reply')->where('reply', '=', Auth::user()->id)
            ->where('system', '=', 1)->lists('order');

            // 过滤重复的工单号
            $order_rs = array_unique($order_rs);
            if (count($order_rs) == 0) {
                $count['3'] = 0;
            } elseif (count($order_rs) == 0) {
                $count['3'] = count($order_rs);
            } else {
                foreach ($order_ds as $k => $v) {
                    foreach ($order_rs as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_rs[$ks]);
                        }
                    }
                }

                $count['3'] = count($order_rs);
            }

            // 个人驳回池工单总数
            // 去除已扔进废纸篓的工单号
            $order_d = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('disabled', '=', 'true')->lists('id');
            $order_r = DB::table($com->id . '_order_reply')->where('reply', '=', Auth::user()->id)
            ->where('system', '=', 2)->lists('order');

            // 过滤重复的工单号
            $order_r = array_unique($order_r);
            if (count($order_r) == 0) {
                $count['4'] = 0;
            } elseif (count($order_r) == 0) {
                $count['4'] = count($order_r);
            } else {
                foreach ($order_d as $k => $v) {
                    foreach ($order_r as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_r[$ks]);
                        }
                    }
                }

                $count['4'] = count($order_r);
            }
            
            // 个人草稿箱工单总数
            $count['5'] = DB::table($com->id . '_order_draft')->where('admin', '=', Auth::user()->id)->count();
            // 个人垃圾箱工单总数
            $count['6'] = DB::table($com->id . '_order')->where('disabled', '=', 'false')->where('reply', '=', Auth::user()->id)
            ->where('trash', '=', 1)->count();
            // 个人废纸篓工单总数
            $count['7'] = DB::table($com->id . '_order')->where('disabled', '=', 'true')
            ->where('reply', '=', Auth::user()->id)->count();
            // 个人银行凭证工单总数
            $count['9'] = DB::table($com->id . '_order')->where('trash', '=', 0)->where('disabled', '=', 'false')
            ->where('bank', '=', 1)->where('reply', '=', Auth::user()->id)->count();
        } elseif ($level == 3) {
            // 获取作为组长的所属组别
            $group = GroupAdminUser::where('user_id', Auth::user()->id)
            ->where('grade', '1')->get()->lists('group_id');

            // 待分配工单总数
            $count['0'] = DB::table($com->id . '_order')->whereIn('group', $group)->where('status', '=', 0)
            ->where('trash', '=', 0)->where('disabled', '=', 'false')->count();

            // 组别待处理工单数
            $g_count_1 = DB::table($com->id . '_order')->whereIn('group', $group)->where('status', '=', 1)
            ->where('trash', '=', 0)->where('reply', '!=', Auth::user()->id)->where('disabled', '=', 'false')->count();

            // 个人待处理工单数
            $m_count_1 = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('status', '=', 1)->where('trash', '=', 0)->where('disabled', '=', 'false')->count();

            // 待处理工单总数
            $count['1'] = $g_count_1 + $m_count_1;

            // 组别已处理工单数
            $g_count_2 = DB::table($com->id . '_order')->whereIn('group', $group)->where('status', '=', 2)
            ->where('trash', '=', 0)->where('reply', '!=', Auth::user()->id)->where('disabled', '=', 'false')->count();

            // 个人已处理工单数
            $m_count_2 = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('status', '=', 2)->where('trash', '=', 0)->where('disabled', '=', 'false')->count();

            // 已处理工单总数
            $count['2'] = $g_count_2 + $m_count_2;

            // 获取所属组工单
            $group_order = Order::whereIn('group', $group)->where('reply', '!=', Auth::user()->id)->get()->lists('id');

            // 获取需要审核的工单号
            $order_reply_o = DB::table($com->id . '_order_reply')->where('system', '=', 1)->lists('order');
            // 过滤重复的工单号
            $order_id_o = array_unique($order_reply_o);
            // 两个或多个数组的交集数组(工单)
            $order_sect_o = array_intersect($order_id_o, $group_order);
            if (count($order_sect_o) == 0) {
                $order_sect_o[0] = 0;
            }

            // 获取需要驳回的工单号
            $order_reply_t = DB::table($com->id . '_order_reply')->where('system', '=', 2)->lists('order');
            // 过滤重复的工单号
            $order_id_t = array_unique($order_reply_t);
            // 两个或多个数组的交集数组(工单)
            $order_sect_t = array_intersect($order_id_t, $group_order);
            if (count($order_sect_t) == 0) {
                $order_sect_t[0] = 0;
            }

            // 去除已扔进废纸篓的需审核工单号
            if ($order_sect_o[0] != 0) {
                $order_d = DB::table($com->id . '_order')->where('disabled', '=', 'true')->lists('id');
                foreach ($order_d as $k => $v) {
                    foreach ($order_sect_o as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_sect_o[$ks]);
                        }
                    }
                }
            }

            // 去除已扔进废纸篓的需驳回工单号
            if ($order_sect_t[0] != 0) {
                $order_d = DB::table($com->id . '_order')->where('disabled', '=', 'true')->lists('id');
                foreach ($order_d as $k => $v) {
                    foreach ($order_sect_t as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_sect_t[$ks]);
                        }
                    }
                }
            }

            // 组别审核池工单总数
            if (count($order_sect_o) == 0) {
                $count['3'] = 0;
            } else {
                $count['3'] = DB::table($com->id . '_order')->whereIn('id', $order_sect_o)->count();
            }
            
            // 组别驳回池工单数
            if (count($order_sect_t) == 0) {
                $g_count_4 = 0;
            } else {
                $g_count_4 = DB::table($com->id . '_order')->whereIn('id', $order_sect_t)->count();
            }

            // 个人驳回池工单数
            // 去除已扔进废纸篓的工单号
            $order_d = DB::table($com->id . '_order')->where('reply', '=', Auth::user()->id)
            ->where('disabled', '=', 'true')->lists('id');
            $order_r = DB::table($com->id . '_order_reply')->where('reply', '=', Auth::user()->id)
            ->where('system', '=', 2)->lists('order');

            // 过滤重复的工单号
            $order_r = array_unique($order_r);
            if (count($order_r) == 0) {
                $m_count_4 = 0;
            } elseif (count($order_r) == 0) {
                $m_count_4 = count($order_r);
            } else {
                foreach ($order_d as $k => $v) {
                    foreach ($order_r as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_r[$ks]);
                        }
                    }
                }

                $m_count_4 = count($order_r);
            }

            // 组别驳回池工单总数
            $count['4'] = $g_count_4 + $m_count_4;

            // 个人草稿箱工单总数
            $count['5'] = DB::table($com->id . '_order_draft')->where('admin', '=', Auth::user()->id)->count();
            // 组别垃圾箱工单总数
            $count['6'] = DB::table($com->id . '_order')->where('disabled', '=', 'false')
            ->whereIn('group', $group)->where('trash', '=', 1)->count();
            // 组别废纸篓工单总数
            $count['7'] = DB::table($com->id . '_order')->where('disabled', '=', 'true')
            ->whereIn('group', $group)->count();
            // 个人银行凭证工单总数
            $count['9'] = DB::table($com->id . '_order')->where('trash', '=', 0)->where('disabled', '=', 'false')
            ->where('bank', '=', 1)->where('reply', '=', Auth::user()->id)->count();
        } else {
            // 公司待分配工单总数
            $count['0'] = DB::table($com->id . '_order')->where('status', '=', 0)
            ->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 公司待处理工单总数
            $count['1'] = DB::table($com->id . '_order')->where('status', '=', 1)
            ->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 公司已处理工单总数
            $count['2'] = DB::table($com->id . '_order')->where('status', '=', 2)
            ->where('trash', '=', 0)->where('disabled', '=', 'false')->count();
            // 去除已扔进废纸篓的工单号
            $order_d = DB::table($com->id . '_order')->where('disabled', '=', 'true')->lists('id');
            // 公司审核池工单总数
            $order_r = DB::table($com->id . '_order_reply')->where('system', '=', 1)->lists('order');
            // 过滤重复的工单号
            $order_r = array_unique($order_r);
            if (count($order_r) == 0) {
                $count['3'] = 0;
            } elseif (count($order_r) == 0) {
                $count['3'] = count($order_r);
            } else {
                foreach ($order_d as $k => $v) {
                    foreach ($order_r as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_r[$ks]);
                        }
                    }
                }

                $count['3'] = count($order_r);
            }

            // 公司驳回池工单总数
            $order_rs = DB::table($com->id . '_order_reply')->where('system', '=', 2)->lists('order');
            // 过滤重复的工单号
            $order_rs = array_unique($order_rs);
            if (count($order_rs) == 0) {
                $count['4'] = 0;
            } elseif (count($order_rs) == 0) {
                $count['4'] = count($order_rs);
            } else {
                foreach ($order_d as $k => $v) {
                    foreach ($order_rs as $ks => $vs) {
                        if ($vs == $v) {
                            unset($order_rs[$ks]);
                        }
                    }
                }

                $count['4'] = count($order_rs);
            }

            // 公司草稿箱工单总数
            $count['5'] = DB::table($com->id . '_order_draft')->where('admin', '=', Auth::user()->id)->count();
            // 公司垃圾箱工单总数
            $count['6'] = DB::table($com->id . '_order')->where('disabled', '=', 'false')
            ->where('trash', '=', 1)->count();
            // 公司废纸篓工单总数
            $count['7'] = DB::table($com->id . '_order')->where('disabled', '=', 'true')->count();
            // 公司银行凭证工单总数
            $count['9'] = DB::table($com->id . '_order')->where('trash', '=', 0)->where('disabled', '=', 'false')
            ->where('bank', '=', 1)->count();
        }

        // 判断是否存在工单邀请
        $orderinvite = OrderInvite::where('reply', Auth::user()->id)->get()->lists('order');
        foreach ($orderinvite as $k => $v) {
            $invite_status = DB::table($com->id . '_order')->where('id', $v)->pluck('status');
            $count[$invite_status] = $count[$invite_status] + 1;
        }

        return $count;
    }

    // 工单标题
    public function orderTitle($com, $level, $status, $search_type, $search_content)
    {
        // 根据工单类型获取标题
        switch ($status) {
            case '0': // 待分配
                $title = $this->getOrderTitle($com, $level, 0, $search_type, $search_content);
                break;
            case '1': // 待处理
                $title = $this->getOrderTitle($com, $level, 1, $search_type, $search_content);
                break;
            case '2': // 已处理
                $title = $this->getOrderTitle($com, $level, 2, $search_type, $search_content);
                break;
            case '3': // 审核池
                $title = $this->getOrderTitle($com, $level, 3, $search_type, $search_content);
                break;
            case '4': // 驳回池
                $title = $this->getOrderTitle($com, $level, 4, $search_type, $search_content);
                break;
            case '5': // 草稿箱
                $title = $this->getOrderTitle($com, $level, 5, $search_type, $search_content);
                break;
            case '6': // 垃圾箱
                $title = $this->getOrderTitle($com, $level, 6, $search_type, $search_content);
                break;
            case '7': // 废纸篓
                $title = $this->getOrderTitle($com, $level, 7, $search_type, $search_content);
                break;
            case '9': // 银行凭证
                $title = $this->getOrderTitle($com, $level, 9, $search_type, $search_content);
                break;
            default:
                $title = '';
                break;
        }

        return $title;
    }

    // 获取工单标题
    public function getOrderTitle($com, $level = NULL, $status = NULL, $search_type = NULL, $search_content = NULL)
    {
        // 点击显示更多 - start
        if (isset($_POST['more']) && $_POST['more'] == 1) {
            $level = $_POST['level'];
            $status = $_POST['status'];

            $take = $this->take;
            $skip = $this->take + $_POST['number'];
        } else {
            $take = $this->take;
            $skip = $this->skip;
        }
        // 点击显示更多 - end

        // 根据工单类型和用户等级获取标题
        if ($status == 0 || $status == 1 || $status == 2) {
            $order_head = DB::table($com->id . '_order')->where($com->id . '_order.status', '=', $status)
            ->where($com->id . '_order.disabled', '=', 'false')->where($com->id . '_order.trash', '=', 0);

            if ($level == 4) {
                // $order_body = $order_head->where($com->id . '_order.reply', '=', Auth::user()->id);

                $m_order = Order::where('reply', Auth::user()->id)->get()->lists('id');
                $orderinvite = OrderInvite::where('reply', Auth::user()->id)->get()->lists('order');
                $orders = array_merge($m_order, $orderinvite);
                if (count($orders) == 0) {
                    $orders[0] = 0;
                }

                $order_body = $order_head->whereIn($com->id . '_order.id', $orders);
            } elseif ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                // $order_body = $order_head->whereIn($com->id . '_order.group', $group);

                $g_order = Order::where('reply', '!=', Auth::user()->id)
                ->whereIn($com->id . '_order.group', $group)->get()->lists('id');
                $m_order = Order::where('reply', Auth::user()->id)->get()->lists('id');
                $orderinvite = OrderInvite::where('reply', Auth::user()->id)->get()->lists('order');
                if ($status == 0) {
                    $orders = $g_order;
                } else {
                    $orders = array_merge($g_order, $m_order, $orderinvite);
                }

                if (count($orders) == 0) {
                    $orders[0] = 0;
                }
                
                $order_body = $order_head->whereIn($com->id . '_order.id', $orders);
            } else {
                $order_body = $order_head;
            }
        } elseif ($status == 3) {
            $order_body = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'false')
            ->where($com->id . '_order.trash', '=', 0);
            if ($level == 4) {
                // 获取需要审核的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 1)
                ->where('reply', '=', Auth::user()->id)->lists('order');
                // 过滤重复的工单号
                $order_sect = array_unique($order_reply);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            } elseif ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                // 获取所属组工单
                $group_order = Order::whereIn('group', $group)->get()->lists('id');
                // 获取需要审核的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 1)->lists('order');
                // 过滤重复的工单号
                $order_id = array_unique($order_reply);
                // 两个或多个数组的交集数组(工单)
                $order_sect = array_intersect($order_id, $group_order);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            } else {
                // 获取需要审核的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 1)->lists('order');
                // 过滤重复的工单号
                $order_sect = array_unique($order_reply);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            }
        } elseif ($status == 4) {
            $order_body = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'false')
            ->where($com->id . '_order.trash', '=', 0);
            if ($level == 4) {
                // 获取需要驳回的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 2)
                ->where('reply', '=', Auth::user()->id)->lists('order');
                // 过滤重复的工单号
                $order_sect = array_unique($order_reply);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            } elseif ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                // 获取需要驳回的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 2)->lists('order');
                // 获取所属组工单
                $group_order = Order::whereIn('group', $group)->get()->lists('id');
                // 过滤重复的工单号
                $order_id = array_unique($order_reply);
                // 两个或多个数组的交集数组(工单)
                $order_sect = array_intersect($order_id, $group_order);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            } else {
                // 获取需要驳回的工单号
                $order_reply = DB::table($com->id . '_order_reply')->where('system', '=', 2)->lists('order');
                // 过滤重复的工单号
                $order_sect = array_unique($order_reply);
                if (count($order_sect) == 0) {
                    $order_sect[0] = 0;
                }
            }
        } elseif ($status == 5) {
            $order_body = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'false')
            ->where($com->id . '_order.trash', '=', 0);

            // 获取带草稿的工单号
            $order_sect = DB::table($com->id . '_order_draft')
            ->where('admin', '=', Auth::user()->id)->lists('order');
            if (count($order_sect) == 0) {
                $order_sect[0] = 0;
            }
        } elseif ($status == 6) {
            $order_head = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'false')
            ->where($com->id . '_order.trash', '=', 1);
            if ($level == 4) {
                $order_body = $order_head->where($com->id . '_order.reply', '=', Auth::user()->id);
            } elseif ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                $order_body = $order_head->whereIn($com->id . '_order.group', $group);
            } else {
                $order_body = $order_head;
            }
        } elseif ($status == 7) {
            $order_head = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'true');
            if ($level == 4) {
                $order_body = $order_head->where($com->id . '_order.reply', '=', Auth::user()->id);
            } elseif ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                $order_body = $order_head->whereIn($com->id . '_order.group', $group);
            } else {
                $order_body = $order_head;
            }
        } elseif ($status == 9) {
            $order_head = DB::table($com->id . '_order')->where($com->id . '_order.disabled', '=', 'false')
            ->where($com->id . '_order.trash', '=', 0)->where($com->id . '_order.bank', '=', 1);

            if ($level > 2) {
                $order_body = $order_head->where($com->id . '_order.reply', '=', Auth::user()->id);
            } else {
                $order_body = $order_head;
            }
        }

        // 是否包含标题搜索
        if ($search_type == 1) {
            $order_end = $order_body->where($com->id . '_order.title', 'like', '%'.trim($search_content).'%');
        } elseif ($search_type == 3) { // 是否包含工单搜索
            $order_end = $order_body->where($com->id . '_order.id', '=', $search_content);
        } elseif ($search_type == 4) { // 是否包含接收内容搜索
            $order_end = $order_body->join($com->id . '_order_ask', $com->id . '_order.id', '=', $com->id . '_order_ask.order')
            ->where($com->id . '_order_ask.content', 'like', '%'.trim($search_content).'%');
        } elseif ($search_type == 5) { // 是否包含回复内容搜索
            $order_end = $order_body->join($com->id . '_order_reply', $com->id . '_order.id', '=', $com->id . '_order_reply.order')
            ->where($com->id . '_order_reply.content', 'like', '%'.trim($search_content).'%');
        } else {
            $order_end = $order_body;
        }

        // 是否包含发件人邮箱搜索
        if ($search_type == 2) {
            $order_footer = $order_end->join('users', 'users.id', '=', $com->id . '_order.ask')
            ->where('users.user_name', 'like', '%'.trim($search_content).'%');
        } elseif ($search_type == 6) { // 是否包含处理人邮箱搜索
            $order_footer = $order_end->join('users', 'users.id', '=', $com->id . '_order.reply')
            ->where('users.user_name', 'like', '%'.trim($search_content).'%');
        } elseif ($search_type == 7) { // 是否包含发件人姓名搜索
            $order_footer = $order_end->join('users', 'users.id', '=', $com->id . '_order.ask')
            ->where('users.name', 'like', '%'.trim($search_content).'%');
        } elseif ($search_type == 8) { // 是否包含处理人姓名搜索
            $order_footer = $order_end->join('users', 'users.id', '=', $com->id . '_order.reply')
            ->where('users.name', 'like', '%'.trim($search_content).'%');
        } else {
            $order_footer = $order_end->join('users', 'users.id', '=', $com->id . '_order.ask');
        }

        if ($status == 0 || $status == 1 || $status == 2 || $status == 6 || $status == 7 || $status == 9) {
            $order_title_data = $order_footer->select($com->id . '_order.*', 'users.user_name', 'users.name')
            ->orderBy($com->id . '_order.updated_at', 'desc');
        } elseif ($status == 3 || $status == 4 || $status == 5) {
            $order_title_data = $order_footer->whereIn($com->id . '_order.id', $order_sect)
            ->select($com->id . '_order.*', 'users.user_name', 'users.name')
            ->orderBy($com->id . '_order.updated_at', 'desc');
        }

        // 显示更多判断是否含有搜索 - start
        // if ($search_type > 0) {
        //     $order_title = $order_title_data->get();
        // } else {
        //     $order_title = $order_title_data->skip($skip)->take($take)->get();
        // }
        // 显示更多判断是否含有搜索 - end

        // 分页显示 - start
        $order_title = $order_title_data->paginate($this->take);
        if ($search_type > 0 && $this->pagecount == 0) {
            $this->pagecount = $order_title_data->count();
        }
        // 分页显示 - end

        // 获取工单等级
        foreach ($order_title as $k => $v) {
            if ($v->level > 0) {
                $order_title[$k]->level_name = DB::table($com->id . '_order_level')
                ->where('id', $v->level)->pluck('name');
            }
        }

        // 获取工单申请人
        foreach ($order_title as $k => $v) {
            if ($v->ask > 0) {
                $user = DB::table('users')->where('id', $v->ask)->first();
                $order_title[$k]->name = $user->name;
                $order_title[$k]->user_name = $user->user_name;
            }
        }
        
        // 获取工单受理人
        foreach ($order_title as $k => $v) {
            if ($v->reply > 0) {
                $admin = DB::table('users')->where('id', $v->reply)->first();
                $order_title[$k]->admin_name = $admin->name;
                $order_title[$k]->admin_user_name = $admin->user_name;
            }
        }

        // 点击显示更多数据组合 - start
        if (isset($_POST['more']) && $_POST['more'] == 1) {
            // ajax组合工单标题数据
            $html_tp = <<<H
%s
H;
            $html = "";
            foreach ($order_title as $k => $v) {
                $title_head = '<div id="title_'.$v->id.'">';
                $title_head .= '<div id="tasklistshow_'.$v->id.'" class="media tasklist">';

                $title_body = '<div class="heading-media">';
                $title_body .= '<span class="order-title-filter">
                                <input type="checkbox" value="'.$v->id.'" name="title_filter" id="idcheck">
                                </span>
                                <a class="order_title" href="javascript:void(0)" onclick="orderAjax('.$v->id.', '.$status.'), tasklistshow('.$v->id.')">
                                <span class="order_title_name" title="'.$v->user_name.'">'.$v->name.'</span>
                                </a>';
                if (isset($v->level_name)) {
                    $title_body .= '<span class="labels-sms">'.$v->level_name.'</span>';
                }

                if ($v->type == 'admin') {
                    $title_body .= '&nbsp;<span class="glyphicon glyphicon-eye-close" title="内部工单"></span>';
                }

                // $title_body .= '<abbr class="order_title_time timeago" title="'.$v->updated_at.'">'.$v->updated_at.'</abbr>';
                $title_body .= '<br /><span class="order_title_time" title="'.$v->updated_at.'"><b>'.$v->updated_at.'</b></span><br />';
                
                $title_end = '</div>';
                if ($v->mark != '') {
                    $title_end .= '<div class="mark-align"><span class="mark-sms">'.$v->mark.'</span></div>';
                }
                
                $title_end .= '<a class="order_title" href="javascript:void(0)" onclick="orderAjax('.$v->id.', '.$status.'), tasklistshow('.$v->id.')">
                                <span class="body-media">'.$v->title.'</span>
                                </a>';

                $title_footer = '</div>';
                if (isset($v->admin_name)) {
                    $title_footer .= '<div class="l11" title="'.$v->admin_user_name.'">
                                    受理人：<a class="order_title" href="#">'.$v->admin_name.'<a/>
                                    </div>>';
                }

                $title_footer .= '<hr class="hr-middle" />';
                $title_footer .= '</div>';

                $title_html = $title_head.$title_body.$title_end.$title_footer;

                $html .= sprintf($html_tp, $title_html);
            }

            // 组合所需状态数据
            $arr = array(
                'more_id' => 1,
                'more_num' => $skip,
                'html' => $html,
            );

            // 结束返回json数据
            exit(json_encode($arr));
        }
        // 点击显示更多数据组合 - end

        return $order_title;
    }

    // 工单内容ajax
    public function getOrderContent($com)
    {
        // 获取工单ID和工单状态
        $id = Input::get('id');
        $status = Input::get('order_status');

        // 获取工单标题
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        $o_title = $order->title;

        // 获取工单所属组
        $o_group = DB::table($com->id . '_groups')->where('id', $order->group)->pluck('group_name');

        // 获取工单类型
        if ($order->type == 'admin') {
            $o_type = '&nbsp;<span class="glyphicon glyphicon-eye-close" title="内部工单"></span>';
        } else {
            $o_type = '';
        }

        // 获取工单申请人
        $ask_name = DB::table('users')->where('id', $order->ask)->first();
        $o_ask = $ask_name->name;
        $o_ask_t = $ask_name->user_name;

        // 获取工单邮箱
        $email_id = DB::table($com->id . '_order_ask')->where('order', $id)->pluck('email');
        $o_email = DB::table('company_email')->where('id', $email_id)->pluck('email');
        if ($o_email == '') {
            $o_email = DB::table('company_email')->where('id', $order->email)->pluck('email');
        }

        // 获取工单受理人
        if ($order->reply > 0) {
            $reply_name = DB::table('users')->where('id', $order->reply)->first();
            $o_reply = $reply_name->name;
            $o_reply_t = $reply_name->user_name;
        } else {
            $o_reply = '无';
            $o_reply_t = '无';
        }

        // 获取提问内容
        $ask = DB::table($com->id . '_order_ask')->join('users', 'users.id', '=', $com->id . '_order_ask.ask')
        ->where($com->id . '_order_ask.order', '=', $id)->select($com->id . '_order_ask.*', 'users.user_name', 
        'users.name', 'users.head')->orderBy($com->id . '_order_ask.created_at', 'desc')->get();

        // 获取回复内容
        $reply = DB::table($com->id . '_order_reply')->join('users', 'users.id', '=', $com->id . 
        '_order_reply.reply')->where($com->id . '_order_reply.order', '=', $id)->select($com->id . 
        '_order_reply.*', 'users.user_name', 'users.name', 'users.head')->orderBy($com->id . 
        '_order_reply.created_at', 'desc')->get();

        // 组合工单内容
        if (count($reply) > 0) {
            // 就合并提问和回复数据并添加对比对象
            $content = array_merge($ask, $reply);
            foreach ($content as $k => $v) {
                $accuracy[$k] = $v->created_at;
            }

            // 倒叙排列工单内容
            array_multisort($accuracy, SORT_DESC, $content);
            // 正叙排列工单内容
            // array_multisort($accuracy, SORT_ASC, $content);
        } else {
            $content = $ask;
        }

        // 获取用户和客户头像
        foreach ($content as $k => $v) {
            if ($v->head > 0) {
                $content[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
            }

            // // 换行符转换
            // if ($v->content != '') {
            //     $content[$k]->content = str_replace("\n", "<br />", $v->content);
            // }
        }

        // ajax组合工单标题数据
        $html_tp_title = <<<H
<div class="order-title-alert order-title-right">
#%s &nbsp;标题：%s %s
<div class="label-ua">
申请人：<span title="%s">%s</span> &nbsp;( %s ) &nbsp;<span class="l1">|</span>&nbsp; 受理人：<span title="%s">%s</span>
 &nbsp;<span class="l1">|</span>&nbsp; 收件箱：<span>%s</span>&nbsp;<span class="l1">|</span>&nbsp; 所属组：<span>%s</span>
</div>
</div>
H;
        $html_title = sprintf($html_tp_title, $id, $o_title, $o_type, $o_ask_t, $o_ask, $o_ask_t, $o_reply_t, $o_reply, $o_email, $o_group);

        // ajax组合工单内容数据
        $html_tp = <<<H
<div class="media">
<a class="pull-left" href="#">
<img class="media-object media-border-radius" src="%s">
</a>
<div class="heading-media">
<a class="name-body-title" title="%s" href="#">%s</a> <span class="time">%s</span> %s %s %s %s
<span class="askreply-id">%s</span>
</div>
<div>%s<div />
<div class="body-media">%s</div>
</div>
<hr class="hr-middle" />
H;
        $html = "";
        foreach ($content as $c) {
            // 获取对应头像
            $askHtml = '';
            if (isset($c->head_path)) {
                $head_image = asset($c->head_path);
            } elseif (isset($c->ask)) {
                $head_image = asset('assets/img/avataru.png');
                $askHtml = '<span class="sphtml glyphicon glyphicon-user" title="客户"></span>&nbsp;';
            } elseif (isset($c->reply)) {
                $head_image = asset('assets/img/avatara.png');
            }

            // 判断是否为审核内容
            if ($c->system == 1) {
                $systemHtml = '<a href="javascript:void(0)" onclick="process('.$c->id.')"><span class="sphtml glyphicon glyphicon-zoom-in" title="待审核"></span></a>';
            } elseif ($c->system == 2) {
                $systemHtml = '<a href="javascript:void(0)" onclick="overrule('.$c->id.')"><span class="sphtml glyphicon glyphicon-zoom-out" title="被驳回"></span></a>';
            } else {
                $systemHtml = '';
            }

            // 判断是否有备注信息
            if (isset($c->ask)) {
                $comment = DB::table($com->id . '_order_remark')->where('order_ask', '=', $c->id)->count();
                $type = 1;
            } elseif (isset($c->reply)) {
                $comment = DB::table($com->id . '_order_remark')->where('order_reply', '=', $c->id)->count();
                $type = 0;
            }

            if ($comment > 0) {
                $remarkHtml = '<a href="javascript:void(0)" onclick="comment('.$c->id.', '.$type.')"><span class="sphtml glyphicon glyphicon-comment" title="备注信息"></span></a>';
            } else {
                $remarkHtml = '';
            }

            // 判断是否有附件信息
            $etype = 0;
            if (isset($c->ask) && $c->attachment != 0 && $c->attachment != 3) {
                $etype = 1;
            } elseif (isset($c->reply) && $c->enclosure != '' && $c->enclosure != 0) {
                $etype = 2;
            }

            if ($etype > 0) {
                if ($c->enclosure != '' && $c->enclosure != 0) {
                    $enclosureHtml = '<a href="javascript:void(0)" onclick="enclosure('.$c->id.', '.$etype.')"><span class="sphtml glyphicon glyphicon-link" title="附件信息"></span></a>';
                } else {
                    $enclosureHtml = '&nbsp;<span class="glyphicon glyphicon-download-alt" title="附件下载中"></span>';
                }
            } else {
                $enclosureHtml = '';
            }

            // 添加备注状态
            if (isset($c->ask)) {
                $addremarkHtml = '<a class="name-body-title" title="添加备注" href="javascript:void(0)" onclick="remark('.$c->id.', 1)">#'.$c->id.'</a>';
            } elseif (isset($c->reply)) {
                $addremarkHtml = '<a class="name-body-title" title="添加备注" href="javascript:void(0)" onclick="remark('.$c->id.', 0)">#'.$c->id.'</a>';
            }

            // $name = $c->name;
            // $user_name = $c->user_name;
            if (isset($c->ask)) {
                $name = $c->name;
                $user_name = $c->user_name;
            } elseif (isset($c->reply)) {
                $name = 'Customer Service';
                $user_name = $c->user_name . ' (' . $c->name . ')';
            }

            // 判断是否含有抄送信息
            if ($c->cc) {
                $c->cc = str_replace('<', "(", $c->cc);
                $c->cc = str_replace('>', ")", $c->cc);
                $cc = '<span style="font-size:12px;color:#456b6a;"><span class="glyphicon glyphicon-subtitles" title="抄送"></span>&nbsp;'.$c->cc.'</span>';
            } else {
                $cc = '';
            }
            
            $created_at = $c->created_at;
            $content = $c->content;
            $html .= sprintf($html_tp, $head_image, $user_name, $name, $created_at, $askHtml, $systemHtml, $remarkHtml, $enclosureHtml, $addremarkHtml, $cc, $content);
        }

        // 查询用户是否将工单保存为草稿
        $draft = DB::table($com->id . '_order_draft')->where('order', $id)->where('admin', Auth::user()->id)->first();
        if ($draft) {
            // $draft_content = str_replace('<br>', "\n", $draft->content);
            $draft_content = $draft->content; // 副文本编辑器
        } else {
            $draft_content = '';
        }

        // 组合所需状态数据
        $arr = array(
            'content_id' => 1,
            'order_id' => $id,
            'draft_content' => $draft_content,
            'html_title' => $html_title,
            'html' => $html,
        );

        // 结束返回json数据
        exit(json_encode($arr));
    }

    // 工单分配
    public function getOrderAllot($com)
    {   
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单ID
        $id = $_GET['id'];

        // 获取工单状态
        $status = $_GET['status'];

        // 获取绑定信息
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        $group = DB::table($com->id . '_groups')->where('id', $order->group)->first();
        $bind = DB::table($com->id . '_bind_user')->where('user_id', $order->ask)
        ->where('admin_id', '>', 0)->first();
        if ($bind == '') {
            $bind_user = 0;
        } else {
            $bind_user = $bind->admin_id;
        }
        
        // 获取用户信息
        $admin_head = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
        ->where($com->id . '_groups.email_id', '>', 0)->join('users', 'users.id', '=', 'admin_user.user_id')
        ->join($com->id . '_group_admin_user', $com->id . '_group_admin_user.user_id', '=', 'admin_user.user_id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id');
        if ($level == 3) {
            // 获取作为组长的所属组别
            $group = GroupAdminUser::where('user_id', Auth::user()->id)
            ->where('grade', '1')->get()->lists('group_id');

            $admin_body = $admin_head->whereIn($com->id . '_group_admin_user.group_id', $group);
        } elseif ($level <= 2) {
            $admin_body = $admin_head;
        }

        $admin = $admin_body->select('users.id', 'users.user_name', 'users.name', 'admin_user.disabled', 
        $com->id . '_group_admin_user.grade', $com->id . '_groups.group_name', $com->id . '_groups.id as group_id', $com->id . '_groups.email_id as email_id')
        ->distinct()->get();

        // 过滤禁用状态下的用户
        foreach ($admin as $k => $v) {
            if ($v->disabled == 'true') {
                unset($admin[$k]);
            }
        }

        // 去除未启用邮箱的组别
        foreach ($admin as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($admin[$k]);
            }
        }

        return View::make('com/admin/order/allot')->with('com', $com)->with(compact('admin'))
        ->with(compact('order'))->with(compact('group'))->with(compact('bind_user'))->with(compact('status'));
    }

    // 工单分配处理
    public function postOrderAllot($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['order_id']);

        // 组合工单数据
        $order->reply = $_GET['admin_id'];
        $order->mark = $_GET['mark'];
        if ($order->status == 0) {
            $order->status = 1;
        }

        // 判断是否垃圾箱分配
        if ($order->trash == 1) {
            $order->trash = 0;
        }
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->people = $_GET['admin_id'];
            if ($_GET['mark'] != '') {
                $log->content = '标记工单并分配给';
            } else {
                $log->content = '将工单分配给';
            }
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单分配成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单分配失败！');
        }
    }

    // 工单转移
    public function getOrderMigrate($com)
    {
        // 获取工单ID
        $id = $_GET['id'];

        // 获取工单状态
        $status = $_GET['status'];

        // 获取绑定信息
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        $group = DB::table($com->id . '_groups')->where('id', $order->group)->first();
        $bind = DB::table($com->id . '_bind_user')->where('user_id', $order->ask)
        ->where('admin_id', '>', 0)->first();
        if ($bind == '') {
            $bind_user = 0;
        } else {
            $bind_user = $bind->admin_id;
        }

        // 获取用户信息
        $admin_head = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
        ->where($com->id . '_groups.email_id', '>', 0)->join('users', 'users.id', '=', 'admin_user.user_id')
        ->join($com->id . '_group_admin_user', $com->id . '_group_admin_user.user_id', '=', 'admin_user.user_id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id');

        $admin_body = $admin_head;
        $admin = $admin_body->select('users.id', 'users.user_name', 'users.name', 'admin_user.disabled', 
        $com->id . '_group_admin_user.grade', $com->id . '_groups.group_name', $com->id . '_groups.id as group_id', $com->id . '_groups.email_id as email_id')
        ->distinct()->get();

        // 过滤禁用状态下的用户
        foreach ($admin as $k => $v) {
            if ($v->disabled == 'true') {
                unset($admin[$k]);
            }
        }

        // 去除未启用邮箱的组别
        foreach ($admin as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($admin[$k]);
            }
        }

        return View::make('com/admin/order/migrate')->with('com', $com)->with(compact('admin'))
        ->with(compact('order'))->with(compact('group'))->with(compact('bind_user'))->with(compact('status'));
    }

    // 工单转移处理
    public function postOrderMigrate($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['order_id']);

        // 组合工单数据
        $order->reply = $_GET['admin_id'];
        $order->mark = $_GET['mark'];
        if ($order->status == 0) {
            $order->status = 1;
        }

        // 判断是否垃圾箱分配
        if ($order->trash == 1) {
            $order->trash = 0;
        }
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->people = $_GET['admin_id'];
            if ($_GET['mark'] != '') {
                $log->content = '标记工单并转移给';
            } else {
                $log->content = '将工单转移给';
            }

            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单转移成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单转移失败！');
        }
    }

    // 工单邀请
    public function getOrderInvite($com)
    {
        // 获取工单ID
        $id = $_GET['id'];

        // 获取工单状态
        $status = $_GET['status'];

        // 获取绑定信息
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        $group = DB::table($com->id . '_groups')->where('id', $order->group)->first();
        $bind = DB::table($com->id . '_bind_user')->where('user_id', $order->ask)
        ->where('admin_id', '>', 0)->first();
        if ($bind == '') {
            $bind_user = 0;
        } else {
            $bind_user = $bind->admin_id;
        }

        // 获取用户信息
        $admin_head = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
        ->where($com->id . '_groups.email_id', '>', 0)->join('users', 'users.id', '=', 'admin_user.user_id')
        ->join($com->id . '_group_admin_user', $com->id . '_group_admin_user.user_id', '=', 'admin_user.user_id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id');

        $admin_body = $admin_head;
        $admin = $admin_body->select('users.id', 'users.user_name', 'users.name', 'admin_user.disabled', 
        $com->id . '_group_admin_user.grade', $com->id . '_groups.group_name', $com->id . '_groups.id as group_id', $com->id . '_groups.email_id as email_id')
        ->distinct()->get();

        // 过滤禁用状态下的用户
        foreach ($admin as $k => $v) {
            if ($v->disabled == 'true') {
                unset($admin[$k]);
            }
        }

        // 去除未启用邮箱的组别
        foreach ($admin as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($admin[$k]);
            }
        }

        return View::make('com/admin/order/invite')->with('com', $com)->with(compact('admin'))
        ->with(compact('order'))->with(compact('group'))->with(compact('bind_user'))->with(compact('status'));
    }

    // 工单邀请处理
    public function postOrderInvite($com)
    {
        // 获取工单状态
        $status = $_GET['status'];
        // 获取工单号
        $order_id = $_GET['order_id'];
        // 获取用户
        $reply = $_GET['admin_id'];

        // 创建邀请对象
        $invite_id = DB::table($com->id . '_order_invite')->where('order', $order_id)->where('reply', $reply)->pluck('id');
        if (!$invite_id) {
            $invite = new OrderInvite;

            // 组合邀请数据
            $invite->order = $order_id;
            $invite->reply = $reply;

            // 保持邀请数据
            $invite->save();
        }

        // 创建工单对象
        $order = Order::find($_GET['order_id']);

        // 组合工单数据
        $order->mark = $_GET['mark'];
        if ($order->status == 0) {
            $order->status = 1;
        }

        // 判断是否垃圾箱分配
        if ($order->trash == 1) {
            $order->trash = 0;
        }
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->people = $_GET['admin_id'];
            if ($_GET['mark'] != '') {
                $log->content = '标记工单并邀请';
            } else {
                $log->content = '工单邀请';
            }

            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单邀请成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单邀请失败！');
        }
    }

    // 工单扔进废纸篓处理
    public function postOrderDisabled($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['id']);

        // 组合工单数据
        $order->disabled = 'true';
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->content = '将工单扔进废纸篓';
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单扔进废纸篓处理成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单扔进废纸篓处理失败！');
        }
    }

    // 标记工单银行凭证处理
    public function postOrderBank($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['id']);

        // 组合工单数据
        $order->bank = 1;
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->content = '标记工单银行凭证';
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '标记工单银行凭证处理成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '标记工单银行凭证处理失败！');
        }
    }

    // 工单移出废纸篓处理
    public function postOrderDisabledOut($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['id']);

        // 组合工单数据
        $order->disabled = 'false';
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->content = '将工单移出废纸篓';
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单移出废纸篓处理成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单移出废纸篓处理失败！');
        }
    }

    // 自定义标签
    public function getOrderTab($com)
    {
        // 获取工单ID
        $id = $_GET['id'];

        // 获取工单状态
        $status = $_GET['status'];

        // 工单是否存在标签
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        
        return View::make('com/admin/order/tab')->with('com', $com)->with(compact('order'))->with(compact('status'));
    }

    // 自定义标签处理
    public function postOrderTab($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['id']);

        // 组合工单数据
        $order->mark = $_GET['mark'];
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->content = '对工单进行了标签设置';
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '设置工单标签成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '设置工单标签失败！');
        }
    }

    // 变更工单级别
    public function getOrderLevelEdit($com)
    {
        // 获取工单ID
        $id = $_GET['id'];

        // 获取工单状态
        $status = $_GET['status'];

        // 获取工单级别
        $order_level = DB::table($com->id . '_order_level')->get();

        // 工单是否存在级别
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        if ($order->level > 0) {
            $order->level_name = DB::table($com->id . '_order_level')->where('id', $order->level)->pluck('name');
        }

        return View::make('com/admin/order/orderleveledit')->with('com', $com)->with(compact('order'))
        ->with(compact('status'))->with(compact('order_level'));
    }

    // 变更工单级别处理
    public function postOrderLevelEdit($com)
    {
        // 获取工单状态
        $status = $_GET['status'];

        // 创建工单对象
        $order = Order::find($_GET['id']);

        // 组合工单数据
        $order->level = $_GET['order_level'];
        
        // 保存工单数据
        if ($order->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order->id;
            $log->admin = Auth::user()->id;
            $log->content = '对工单进行了级别调整';
            
            // 保存工单日志
            $log->save();

            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '变更工单级别成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '变更工单级别失败！');
        }
    }

    // 工单审核
    public function getProcess($com)
    {
        // $reply = DB::table($com->id . '_order_reply')->join('users', 'users.id', '=', $com->id . 
        // '_order_reply.reply')->where($com->id . '_order_reply.id', '=', $_GET['id'])->select($com->id . 
        // '_order_reply.*', 'users.user_name', 'users.name', 'users.head')->get();
        // foreach ($reply as $k => $v) {
        //     if ($v->head > 0) {
        //         $reply[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
        //     }

        //     // 换行符转换
        //     if ($v->content != '') {
        //         $reply[$k]->content = str_replace("\n", "<br />", $v->content);
        //     }
        // }

        // $reply = $reply[0];
        $status = $_GET['status'];
        $id = $_GET['id'];
        return View::make('com/admin/order/process')->with('com', $com)->with(compact('reply'))
        ->with(compact('status'))->with(compact('id'));
    }

    // 工单审核处理
    public function postProcess($com)
    {
        // 创建工单回复对象
        $reply = OrderReply::find($_GET['id']);
        $status = $_GET['status'];
        $config = 0;

        // 组合工单回复数据
        if ($_GET['system'] == 0) {
            $reply->system = 0;
        } elseif ($_GET['system'] == 3) {
            // js解码
            $j_content = $this->unescape($_GET['remark']);
            $reply->content = str_replace("\n", "<br>", $j_content);
            $reply->system = 0;
        } elseif ($_GET['system'] == 2) {
            $reply->system = 2;
        }
        
        // 保存工单回复数据
        if ($reply->save()) {
            // 备注信息处理
            if ($_GET['remark'] != '' && $_GET['system'] != 3) {
                // js解码
                $j_content = $this->unescape($_GET['remark']);
                $content = str_replace("\n", "<br>", $j_content);

                // 创建工单备注对象
                $remark = new OrderRemark;

                // 组合工单备注数据
                $remark->order = $reply->order;
                $remark->order_reply = $reply->id;
                $remark->admin = Auth::user()->id;
                $remark->content = $content;

                // 保存工单备注数据
                if ($remark->save()) {
                    $config = 0;
                } else {
                    $config = 1;
                }
            }

            // 工单队列邮件状态发送变更
            if ($_GET['system'] == 0) {
                DB::table($com->id . '_order_quere')->where('reply', $reply->id)
                ->update(array('process' => 0));
            } elseif ($_GET['system'] == 3) {
                // js解码
                $j_content = $this->unescape($_GET['remark']);
                $quere_content = str_replace("\n", "<br>", $j_content);

                DB::table($com->id . '_order_quere')->where('reply', $reply->id)
                ->update(array('process' => 0, 'content' => $quere_content));
            }

            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $reply->order;
            $log->admin = Auth::user()->id;
            if ($_GET['system'] == 0) {
                $log->content = '对工单进行了审核，审核结果：成功!';
            } elseif ($_GET['system'] == 3) {
                $log->content = '对审核工单直接进行了修改回复!';
            } else {
                $log->content = '对工单进行了审核，审核结果：驳回!';
            }
            
            // 保存工单日志
            if ($log->save()) {
                $config = 0;
            } else {
                $config = 1;
            }
        } else {
            $config = 1;
        }

        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '审核工单成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '审核工单失败！');
        }
    }

    // 工单驳回
    public function getOverrule($com)
    {
        $reply = DB::table($com->id . '_order_reply')->join('users', 'users.id', '=', $com->id . 
        '_order_reply.reply')->where($com->id . '_order_reply.id', '=', $_GET['id'])->select($com->id . 
        '_order_reply.*', 'users.user_name', 'users.name', 'users.head')->get();
        foreach ($reply as $k => $v) {
            if ($v->head > 0) {
                $reply[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
            }

            // // 换行符转换
            // if ($v->content != '') {
            //     $reply[$k]->content = str_replace("<br>", "\n", $v->content);
            // }
        }

        $reply = $reply[0];
        $status = $_GET['status'];
        $id = $_GET['id'];

        // 获取原附件信息
        if ($reply->enclosure) {
            $enclosure = explode(',', $reply->enclosure);
            foreach ($enclosure as $k => $v) {
                $reply->oldenclosure[$k]['id'] = $v;
                $reply->oldenclosure[$k]['name'] = DB::table('enclosures')->where('id', $v)->pluck('enclosure_name');
            }
        }

        return View::make('com/admin/order/overrule')->with('com', $com)->with(compact('reply'))
        ->with(compact('status'))->with(compact('id'));
    }

    // 工单驳回处理
    public function postOverrule($com)
    {
        // 创建工单回复对象
        $id = Input::get('id');
        $status = Input::get('status');
        $system = Input::get('system');
        $old_enclosure = explode(',', Input::get('old_enclosure'));
        $content = Input::get('content');
        $cc = Input::get('cc');
        $reply = OrderReply::find($id);
        $config = 0;

        // 判断是否带附件
        $file_status = 0; // 上传开关
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            foreach ($file as $k => $v) {
                // 验证附件大小
                if ($v->getSize() > (2 * 1024 * 1024)) {
                    return Redirect::to($com->path . '/admin/order?status='.$status)
                    ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                }
            }

            $file_status = 1;
        }

        // 判断是否删除附件
        if (Input::get('oldenclosure')) {
            foreach ($old_enclosure as $k => $v) {
                foreach (Input::get('oldenclosure') as $key => $value) {
                    if ($v == $value) {
                        unset($old_enclosure[$k]);
                    }
                }
            }
        }

        // 判断是否需要上传
        $fileID = array();
        if ($file_status == 1) {
            foreach ($file as $k => $v) {
                // 获得上传路径
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;

                // 获取文件类型
                $mimeType = explode('/', $v->getmimeType());
                if ($mimeType[0] == 'image') {
                    $file_type = $mimeType[0];
                } else {
                    $file_type = 'file';
                }
                
                // 文件上传处理
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;
                $extension = $v->getClientOriginalExtension();

                $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;

                // $upload_success = $v->move($destinationPath, $ClientOriginalName);
                $upload_success = 1;
                if ($upload_success) {
                    $encl = new Enclosure();
                    $encl->enclosure_name = $v->getClientOriginalName();
                    $encl->type = $file_type;
                    $encl->suffix = $extension;
                    $encl->path = $destinationPath . '/' . $ClientOriginalName;
                    $encl->reply = Auth::user()->id;
                    $encl->order = $reply->order;

                    if ($encl->save()) {
                        $fileID[] = $encl->id;
                    }
                }
            }
        }

        // 定义附件参数
        $reply_enclosure = '';
        if (count($fileID) > 0) {
            $fileIDs = array_merge($fileID, $old_enclosure);
            foreach ($fileIDs as $k) {
                $reply_enclosure .= $k . ',';
            }

            $reply_enclosure = rtrim($reply_enclosure, ',');
        } else {
            foreach ($old_enclosure as $k) {
                $reply_enclosure .= $k . ',';
            }

            $reply_enclosure = rtrim($reply_enclosure, ',');
        }

        // 组合工单回复数据
        $reply->system = $system;
        $reply->content = $content;
        $reply->cc = $cc;
        $reply->enclosure = $reply_enclosure;

        // 保存工单回复数据
        if ($reply->save()) {
            // 修改邮件队列内容
            if ($quere_id = DB::table($com->id . '_order_quere')->where('reply', $id)->pluck('id')) {
                // 邮件队列对象
                $quere = OrderQuere::find($quere_id);

                // 组合邮件队列数据
                $quere->content = $content;
                $quere->cc = $cc;

                // 保存邮件队列数据
                $quere->save();
            }

            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $reply->order;
            $log->admin = Auth::user()->id;
            $log->content = '对驳回工单进行了修改';

            // 保存工单日志
            if ($log->save()) {
                $config = 0;
            } else {
                $config = 1;
            }
        } else {
            $config = 1;
        }

        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '驳回工单处理成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '驳回工单处理失败！');
        }
    }

    // 备注信息
    public function getComment($com)
    {
        // 获取带备注的ID号和内容
        if ($_GET['type'] == 0) {
            $remark_id = DB::table($com->id . '_order_remark')->where('order_reply', '=', $_GET['id'])->lists('id');
            // $content = DB::table($com->id . '_order_reply')->join('users', 'users.id', '=', $com->id . 
            // '_order_reply.reply')->where($com->id . '_order_reply.id', '=', $_GET['id'])->select($com->id . 
            // '_order_reply.*', 'users.user_name', 'users.name', 'users.head')->get();
            // foreach ($content as $k => $v) {
            //     if ($v->head > 0) {
            //         $content[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
            //     }
            // }
        } elseif ($_GET['type'] == 1) {
            $remark_id = DB::table($com->id . '_order_remark')->where('order_ask', '=', $_GET['id'])->lists('id');
            // $content = DB::table($com->id . '_order_ask')->join('users', 'users.id', '=', $com->id . 
            // '_order_ask.ask')->where($com->id . '_order_ask.id', '=', $_GET['id'])->select($com->id . 
            // '_order_ask.*', 'users.user_name', 'users.name', 'users.head')->get();
            // foreach ($content as $k => $v) {
            //     if ($v->head > 0) {
            //         $content[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
            //     }
            // }
        }

        $remark = DB::table($com->id . '_order_remark')->join('users', 'users.id', '=', $com->id . 
        '_order_remark.admin')->whereIn($com->id . '_order_remark.id', $remark_id)->select($com->id . 
        '_order_remark.*', 'users.user_name', 'users.name', 'users.head')->get();
        foreach ($remark as $k => $v) {
            if ($v->head > 0) {
                $remark[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
            }
        }

        // $content = $content[0];
        $status = $_GET['status'];
        $type = $_GET['type'];
        return View::make('com/admin/order/comment')->with('com', $com)->with(compact('content'))
        ->with(compact('type'))->with(compact('remark'))->with(compact('status'));
    }

    // 附件信息
    public function getEnclosure($com)
    {
        // 判断上传附件对象
        if ($_GET['type'] == 2) {
            // 获取工单处理数据
            $reply = DB::table($com->id . '_order_reply')->where('id', '=', $_GET['id'])->get();

            // 获取处理人数据
            $user_id = $reply[0]->reply;
            $user = DB::table('users')->where('id', '=', $user_id)->get();
            $people = $user[0];
            $people->admin = 1;
            
            // 获取上传附件ID
            $enclosure_id = $reply[0]->enclosure;
            $enclosure_data = explode(',', $enclosure_id);
        } elseif ($_GET['type'] == 1) {
            // 获取工单处理数据
            $ask = DB::table($com->id . '_order_ask')->where('id', '=', $_GET['id'])->get();

            // 获取处理人数据
            $user_id = $ask[0]->ask;
            $user = DB::table('users')->where('id', '=', $user_id)->get();
            $people = $user[0];
            $people->admin = 0;
            
            // 获取上传附件ID
            $enclosure_id = $ask[0]->enclosure;
            $enclosure_data = explode(',', $enclosure_id);
        }

        // 获取上传附件数据
        $enclosure = DB::table('enclosures')->whereIn('id', $enclosure_data)->get();

        // 获取头像
        if ($people->head > 0) {
            $people->head_path = DB::table('enclosures')->where('id', $people->head)->pluck('path');
        }

        return View::make('com/admin/order/enclosure')->with('com', $com)
        ->with(compact('people'))->with(compact('enclosure'));
    }

    // 工单往来附件
    public function getOrderEnclosure($com)
    {
        // 获取上传附件数据
        $enclosure = DB::table('enclosures')->where('order', $_GET['id'])->get();
        foreach ($enclosure as $k => $v) {
            if ($v->ask > 0) {
                $enclosure[$k]->admin = 0;
                $user = DB::table('users')->where('id', '=', $v->ask)->get();
            } elseif ($v->reply > 0) {
                $enclosure[$k]->admin = 1;
                $user = DB::table('users')->where('id', '=', $v->reply)->get();
            }

            $people = $user[0];
            $enclosure[$k]->user_name = $people->user_name;
            $enclosure[$k]->name = $people->name;

            // 获取头像
            if ($people->head > 0) {
                $enclosure[$k]->head_path = DB::table('enclosures')->where('id', $people->head)->pluck('path');
            }
        }

        return View::make('com/admin/order/orderenclosure')->with('com', $com)->with(compact('enclosure'));
    }

    // 工单备注
    public function getRemark($com)
    {
        // if ($_GET['type'] == 0) {
        //     $content = DB::table($com->id . '_order_reply')->join('users', 'users.id', '=', $com->id . 
        //     '_order_reply.reply')->where($com->id . '_order_reply.id', '=', $_GET['id'])->select($com->id . 
        //     '_order_reply.*', 'users.user_name', 'users.name', 'users.head')->get();
        //     foreach ($content as $k => $v) {
        //         if ($v->head > 0) {
        //             $content[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
        //         }
        //     }
        // } elseif($_GET['type'] == 1) {
        //     $content = DB::table($com->id . '_order_ask')->join('users', 'users.id', '=', $com->id . 
        //     '_order_ask.ask')->where($com->id . '_order_ask.id', '=', $_GET['id'])->select($com->id . 
        //     '_order_ask.*', 'users.user_name', 'users.name', 'users.head')->get();
        //     foreach ($content as $k => $v) {
        //         if ($v->head > 0) {
        //             $content[$k]->head_path = DB::table('enclosures')->where('id', $v->head)->pluck('path');
        //         }
        //     }
        // }

        // $content = $content[0];
        
        // // 换行符转换
        // $content->content = str_replace("\n", "<br />", $content->content);

        $status = $_GET['status'];
        $type = $_GET['type'];
        $id = $_GET['id'];
        return View::make('com/admin/order/remark')->with('com', $com)->with(compact('type'))
        ->with(compact('content'))->with(compact('status'))->with(compact('id'));
    }

    // 工单备注处理
    public function postRemark($com)
    {
        // 创建工单备注对象
        $remark = new OrderRemark;
        $config = 0;
        $status = $_GET['status'];

        // 组合工单备注数据
        $j_content = $this->unescape($_GET['content']); // js解码
        $content = str_replace("\n", "<br>", $j_content);
        if ($_GET['type'] == 0) {
            $data = DB::table($com->id . '_order_reply')->where('id', $_GET['id'])->first();
            $remark->order_reply = $data->id;
        } elseif ($_GET['type'] == 1) {
            $data = DB::table($com->id . '_order_ask')->where('id', $_GET['id'])->first();
            $remark->order_ask = $data->id;
        }

        $remark->order = $data->order;
        $remark->admin = Auth::user()->id;
        $remark->content = $content;

        // 保存工单备注数据
        if ($remark->save()) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $data->order;
            $log->admin = Auth::user()->id;
            $log->content = '对工单进行了备注';

            // 保存工单日志
            if ($log->save()) {
                $config = 0;
            } else {
                $config = 1;
            }
        } else {
            $config = 1;
        }

        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单备注成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单备注失败！');
        }
    }

    // 工单草稿存储
    public function postDraftCreate($com)
    {
        $id = $_GET['id'];
        $status = $_GET['status'];
        $config = 0;

        // 创建工单草稿对象
        $data = DB::table($com->id . '_order_draft')->where('order', $id)->where('admin', Auth::user()->id)->first();
        if (!$data) {
            $draft = new OrderDraft;
            $draft->order = $id;
            $draft->admin = Auth::user()->id;
        } else {
            $draft = OrderDraft::find($data->id);
        }
        
        // 组合工单草稿数据
        $j_content = $this->unescape($_GET['content']); // js解码
        $draft->content = str_replace("\n", "<br>", $j_content);
        
        // 保存工单草稿数据
        if ($draft->save()) {
            $config = 0;
        } else {
            $config = 1;
        }

        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '保存工单草稿成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '保存工单草稿失败！');
        }
    }

    // 工单草稿删除
    public function postDraftDelete($com)
    {
        $id = $_GET['id'];
        $status = $_GET['status'];

        // 工单草稿删除处理
        if (DB::table($com->id . '_order_draft')->where('order', $id)->where('admin', Auth::user()->id)->delete()) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '删除工单草稿成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '删除工单草稿失败！');
        }
    }

    // 工单批量操作
    public function getFilter($com)
    {
        $type = $_GET['type'];
        $ids = $_GET['ids'];
        $status = $_GET['status'];
        
        if ($type == 1) { // 工单批量扔进废纸篓
            // 分割成数组
            $order_id = explode(",", $ids); 

            // 更新数据
            $config = 0;
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('disabled' => 'true'))) {
                $config = 0;
            } else {
                $config = 1;
            }

            // 批量记录工单日志
            foreach ($order_id as $v) {
                // 创建工单日志对象
                $log = new OrderLog;

                // 组合工单日志数据
                $log->order = $v;
                $log->admin = Auth::user()->id;
                $log->content = '将工单扔进废纸篓';

                // 保存工单日志
                $log->save();
            }

            if ($config == 0) {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单批量扔进废纸篓操作成功！');
            } else {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单批量扔进废纸篓操作失败！');
            }
        } elseif ($type == 2) { // 工单批量移出废纸篓
            // 分割成数组
            $order_id = explode(",", $ids); 

            // 更新数据
            $config = 0;
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('disabled' => 'false'))) {
                $config = 0;
            } else {
                $config = 1;
            }

            // 批量记录工单日志
            foreach ($order_id as $v) {
                // 创建工单日志对象
                $log = new OrderLog;

                // 组合工单日志数据
                $log->order = $v;
                $log->admin = Auth::user()->id;
                $log->content = '将工单移出废纸篓';

                // 保存工单日志
                $log->save();
            }

            if ($config == 0) {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单批量移出废纸篓操作成功！');
            } else {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单批量移出废纸篓操作失败！');
            }
        }  elseif ($type == 3) { // 工单批量移进已处理
            // 分割成数组
            $order_id = explode(",", $ids); 

            // 更新数据
            $config = 0;
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('status' => 2, 'reply' => Auth::user()->id))) {
                $config = 0;
            } else {
                $config = 1;
            }

            // 批量记录工单日志
            foreach ($order_id as $v) {
                // 创建工单日志对象
                $log = new OrderLog;

                // 组合工单日志数据
                $log->order = $v;
                $log->admin = Auth::user()->id;
                $log->content = '将工单移进已处理';

                // 保存工单日志
                $log->save();
            }

            if ($config == 0) {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单批量移进已处理操作成功！');
            } else {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单批量移进已处理操作失败！');
            }
        } elseif ($type == 0) { // 工单批量分配
            // 获取管理员等级
            $level = Config::get('auth.com_admin_level_id');

            // 获取用户信息
            $admin_head = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
            ->join('users', 'users.id', '=', 'admin_user.user_id')
            ->join($com->id . '_group_admin_user', $com->id . '_group_admin_user.user_id', '=', 'admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id');
            if ($level == 3) {
                // 获取作为组长的所属组别
                $group = GroupAdminUser::where('user_id', Auth::user()->id)
                ->where('grade', '1')->get()->lists('group_id');

                $admin_body = $admin_head->whereIn($com->id . '_group_admin_user.group_id', $group);
            } elseif ($level <= 2) {
                $admin_body = $admin_head;
            }

            $admin = $admin_body->select('users.id', 'users.user_name', 'users.name', 'admin_user.disabled', 
            $com->id . '_group_admin_user.grade', $com->id . '_groups.group_name', $com->id . '_groups.id as group_id',  $com->id . '_groups.email_id as email_id')
            ->distinct()->get();

            // 过滤禁用状态下的用户
            foreach ($admin as $k => $v) {
                if ($v->disabled == 'true') {
                    unset($admin[$k]);
                }
            }

            // 去除未启用邮箱的组别
            foreach ($admin as $k => $v) {
                if (DB::table('company_email')->where('company_id', $com->id)
                    ->where('id', $v->email_id)
                    ->where('disabled', '=', 'true')->pluck('id')) {
                    unset($admin[$k]);
                }
            }

            return View::make('com/admin/order/orderallotall')->with('com', $com)->with(compact('admin'))
            ->with(compact('ids'))->with(compact('group'))->with(compact('status'));
        } elseif ($type == 4) { // 工单批量转移
            // 获取用户信息
            $admin_head = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
            ->join('users', 'users.id', '=', 'admin_user.user_id')
            ->join($com->id . '_group_admin_user', $com->id . '_group_admin_user.user_id', '=', 'admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id');
            
            $admin_body = $admin_head;
            $admin = $admin_body->select('users.id', 'users.user_name', 'users.name', 'admin_user.disabled', 
            $com->id . '_group_admin_user.grade', $com->id . '_groups.group_name', $com->id . '_groups.id as group_id')
            ->distinct()->get();

            // 过滤禁用状态下的用户
            foreach ($admin as $k => $v) {
                if ($v->disabled == 'true') {
                    unset($admin[$k]);
                }
            }

            return View::make('com/admin/order/ordermigrateall')->with('com', $com)->with(compact('admin'))
            ->with(compact('ids'))->with(compact('group'))->with(compact('status'));
        } elseif ($type == 5) { // 工单批量取消银行凭证
            // 分割成数组
            $order_id = explode(",", $ids); 

            // 更新数据
            $config = 0;
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('bank' => 0))) {
                $config = 0;
            } else {
                $config = 1;
            }

            // 批量记录工单日志
            foreach ($order_id as $v) {
                // 创建工单日志对象
                $log = new OrderLog;

                // 组合工单日志数据
                $log->order = $v;
                $log->admin = Auth::user()->id;
                $log->content = '取消工单银行凭证';

                // 保存工单日志
                $log->save();
            }

            if ($config == 0) {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '批量取消工单银行凭证操作成功！');
            } else {
                return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '批量取消工单银行凭证操作失败！');
            }
        }
    }

    // 工单批量分配处理
    public function postOrderAllotAll($com)
    {
        $status = $_GET['status'];
        $reply = $_GET['admin_id'];
        $order_id = explode(",", $_GET['ids']);

        // 更新数据
        $config = 0;
        if ($status == 0) { // 判断是否工单池分配
            DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('status' => 1, 'reply' => $reply));
        } elseif ($status == 6) { // 判断是否垃圾箱分配
            DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('status' => 1, 'trash' => 0, 'reply' => $reply));
        } else {
            DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('reply' => $reply));
        }

        // 批量记录工单日志
        foreach ($order_id as $v) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $v;
            $log->admin = Auth::user()->id;
            $log->people = $_GET['admin_id'];
            $log->content = '将工单分配给';

            // 保存工单日志
            if ($log->save()) {
                $config = 0;
            } else {
                $config = 1;
            }
        }

        // 保存工单数据
        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单批量分配成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单批量分配失败！');
        }
    }

    // 工单批量转移处理
    public function postOrderMigrateAll($com)
    {
        $status = $_GET['status'];
        $reply = $_GET['admin_id'];
        $order_id = explode(",", $_GET['ids']);

        // 更新数据
        $config = 0;
        if ($status == 0) { // 判断是否工单池转移
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('status' => 1, 'reply' => $reply))) {
                $config = 0;
            } else {
                $config = 1;
            }
        } elseif ($status == 6) { // 判断是否垃圾箱转移
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('status' => 1, 'trash' => 0, 'reply' => $reply))) {
                $config = 0;
            } else {
                $config = 1;
            }
        } else {
            if (DB::table($com->id . '_order')->whereIN('id', $order_id)->update(array('reply' => $reply))) {
                $config = 0;
            } else {
                $config = 1;
            }
        }

        // 批量记录工单日志
        foreach ($order_id as $v) {
            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $v;
            $log->admin = Auth::user()->id;
            $log->people = $_GET['admin_id'];
            $log->content = '将工单转移给';

            // 保存工单日志
            $log->save();
        }

        // 保存工单数据
        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单批量转移成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单批量转移失败！');
        }
    }

    // 工单回复处理
    public function postOrderReply($com)
    {
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单状态和ID
        $status = Input::get('status');
        $id = Input::get('id');

        // 判断是否带附件
        $file_status = 0; // 上传开关
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            foreach ($file as $k => $v) {
                // 验证附件大小
                if ($v->getSize() > (2 * 1024 * 1024)) {
                    return Redirect::to($com->path . '/admin/order?status='.$status)
                    ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                }
            }

            $file_status = 1;
        }

        // 判断是否需要上传
        $fileID = array();
        if ($file_status == 1) {
            foreach ($file as $k => $v) {
                // 获得上传路径
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;

                // 获取文件类型
                $mimeType = explode('/', $v->getmimeType());
                if ($mimeType[0] == 'image') {
                    $file_type = $mimeType[0];
                } else {
                    $file_type = 'file';
                }

                // 文件上传处理
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;
                $extension = $v->getClientOriginalExtension();

                $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;

                $upload_success = $v->move($destinationPath, $ClientOriginalName);
                if ($upload_success) {
                    $encl = new Enclosure();
                    $encl->enclosure_name = $v->getClientOriginalName();
                    $encl->type = $file_type;
                    $encl->suffix = $extension;
                    $encl->path = $destinationPath . '/' . $ClientOriginalName;
                    $encl->reply = Auth::user()->id;
                    $encl->order = $id;

                    if ($encl->save()) {
                        $fileID[] = $encl->id;
                    }
                }
            }
        }

        // 定义附件参数
        $reply_enclosure = '';
        if (count($fileID) > 0) {
            foreach ($fileID as $k) {
                $reply_enclosure .= $k . ',';
            }

            $reply_enclosure = rtrim($reply_enclosure, ',');
        }

        // 获取回复数据信息
        // $content = str_replace("\r\n", "<br>", Input::get('content'));
        $content = Input::get('content');
        $cc = rtrim(Input::get('cc'), ',');
        $remark_content = '';
        if (Input::get('replyremark_content') != '') {
            $remark_content = str_replace("\r\n", "<br>", Input::get('replyremark_content'));
        }

        // 判断回复内容是否需要审核
        $system = 0; // 审核开关
        $order = DB::table($com->id . '_order')->where('id', $id)->first();
        $check = DB::table($com->id . '_groups')->where('id', $order->group)->pluck('check');
        if ($check == '1' && $order->type == 'user') { // 需要审核
            if ($level == 4) {
                $admin_check = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('check');
                if ($admin_check == '1') { // 判断客服是否设置为需要审核
                    $system = 1;
                }
            }
        }

        // 工单池回复变更工单受理人信息
        if ($status == 0) {
            DB::table($com->id . '_order')->where('id', $id)->update(array('reply' => Auth::user()->id));
        }

        // 创建回答对象
        $reply = new OrderReply;
        $config = 0;

        // 组合回答数据
        $reply->order = $id;
        $reply->reply = Auth::user()->id;
        $reply->content = $content;
        $reply->system = $system;
        $reply->enclosure = $reply_enclosure;
        $reply->email = DB::table($com->id . '_order')->where('id', $id)->pluck('email');
        if ($cc != '') {
            $reply->cc = $cc;
        }
        
        // 保存回答数据
        if ($reply->save()) {
            // 判断是否带备注内容
            if ($remark_content != '') {
                // 创建工单备注对象
                $remark = new OrderRemark;
                
                // 组合工单备注数据
                $remark->order_reply = $reply->id;
                $remark->order = $id;
                $remark->admin = Auth::user()->id;
                $remark->content = $remark_content;

                // 保存工单备注数据
                $remark->save();
            }
            
            $email_id = DB::table($com->id . '_groups')->where('id', $order->group)->pluck('email_id');
            if ($email_id > 0) {
                if ($order->type == 'admin') {
                    $from_email = Auth::user()->user_name;
                } else {
                    // 获取工单所获取到的邮箱名
                    $emailID = DB::table($com->id . '_order_ask')->where('order', $id)->pluck('email');
                    if ($emailID) {
                        $from_email = DB::table('company_email')->where('id', $emailID)->pluck('email');
                    } else {
                        $from_email = DB::table('company_email')->where('id', $email_id)->pluck('email');
                    }
                }

                // 查询客户最新的一条邮件回复 - start
                $ask_id = DB::table($com->id . '_order_ask')->where('order', $id)->max('id');
                if ($ask_id) {
                    $from_content = "";
                    $from_content_data = DB::table($com->id . '_order_ask')->where('id', $ask_id)->first();
                    $member_user = DB::table('users')->where('id', $from_content_data->ask)->first();
                    $from_content .= "<div style='font-size:12px;background-color:#f5f5f5;padding:5px;word-wrap:break-word;margin-top:30px;width:100%;border-radius:4px;'><br />On ".$from_content_data->created_at.", ".$member_user->name." <<a href='mailto:".$member_user->user_name."'>".$member_user->user_name."</a>> wrote:<br /><br />";
                    $from_content .= $from_content_data->content."</div>";
                    $from_content = $content.$from_content;
                }
                // 查询客户最新的一条邮件回复 - end

                // 创建队列对象
                $quere = new OrderQuere;

                // 组合队列数据
                $quere->company_id = $com->id;
                $quere->byname = $com->byname;
                $quere->order = $id;
                $quere->reply = $reply->id;
                $quere->title = 'Re: #'.$id.': '.$order->title;
                $quere->from_email = $from_email;
                $quere->to_email = DB::table('users')->where('id', $order->ask)->pluck('user_name');
                // $quere->content = $content;
                $quere->content = isset($from_content) ? $from_content : $content;
                $quere->process = $system;
                $quere->enclosure = $reply_enclosure;
                if ($cc != '') {
                    $quere->cc = $cc;
                }

                // 保存队列数据
                if ($quere->save()) {
                    // 创建工单对象
                    $orderData = Order::find($id);

                    // 组合工单数据
                    $orderData->status = 2;

                    // 保存工单数据
                    if ($orderData->save()) {
                        $config = 0;
                    } else {
                        $config = 1;
                    }
                } else {
                    $config = 1;
                }

                // 判断是否有工单草稿
                $draft = DB::table($com->id . '_order_draft')->where('order', $id)->lists('id');
                if ($draft) {
                    // 工单草稿删除处理
                    if (DB::table($com->id . '_order_draft')->whereIN('id', $draft)->delete()) {
                        $config = 0;
                    } else {
                        $config = 1;
                    }
                }

                // 创建工单日志对象
                $log = new OrderLog;

                // 组合工单日志数据
                $log->order = $order->id;
                $log->admin = Auth::user()->id;
                $log->content = '对工单进行了回复';

                // 保存工单日志
                if ($log->save()) {
                    $config = 0;
                } else {
                    $config = 1;
                }
            }
        } else {
            // 创建工单对象
            $orderData = Order::find($id);

            // 组合工单数据
            $orderData->status = 2;

            // 保存工单数据
            if ($orderData->save()) {
                $config = 0;
            } else {
                $config = 1;
            }
        }

        // // 组合状态数据
        // if ($config == 1) {
        //     $arr = array(
        //         'config_id' => 1,
        //         'status_id' => $status,
        //     );
        // } else {
        //     $arr = array(
        //         'config_id' => 0,
        //         'status_id' => $status,
        //     );
        // }

        // // 结束返回json数据
        // exit(json_encode($arr));

        // 加载模板
        if ($config == 0) {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('success', '工单回复成功！');
        } else {
            return Redirect::to($com->path . '/admin/order?status='.$status)->with('error', '工单回复失败！');
        }
    }

    // 新建工单
    public function getCreateOrder($com)
    {
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单等级
        $order_level = DB::table($com->id . '_order_level')->where('disabled', '=', 'false')->get();
        
        // 工单总数
        $count = $this->orderCount($com, $level);

        // 获取客户和用户信息
        if ($level > 2) {
            $group = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            $admin = AdminUser::where('company_id', $com->id)->join('users', 'users.id', '=', 'admin_user.user_id')
            ->join($com->id . '_group_admin_user', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->whereIn($com->id . '_group_admin_user.group_id', $group)
            ->select('users.id', 'users.user_name', 'users.name')->distinct()->get();
        } elseif ($level < 3) {
            // 获取用户信息
            $admin = AdminUser::where('company_id', $com->id)->where('admin_user.level', '>', 2)
            ->join('users', 'users.id', '=', 'admin_user.user_id')
            ->select('users.id', 'users.user_name', 'users.name')->distinct()->get();
        }

        // 获取组信息
        if ($level > 2) {
            $group = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            $groups = Group::whereIn('id', $group)->where('email_id', '>', 0)->get();
        } elseif ($level < 3) {
            $groups = Group::where('email_id', '>', 0)->get();
        }

        // 去除未启用邮箱的组别
        foreach ($groups as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($groups[$k]);
            }
        }

        // 新建工单标识
        $os = 8;

        // 加载模版
        $user = 0; // 暂时处理用于500错误
        if ($level == 4) {
            return View::make('com/admin/order/createOrderLevel')->with('com', $com)->with(compact('user'))->with(compact('os'))
            ->with(compact('admin'))->with(compact('order_level'))->with(compact('count'))->with(compact('level'))->with(compact('groups'));
        } else {
            return View::make('com/admin/order/createOrder')->with('com', $com)->with(compact('user'))->with(compact('os'))
            ->with(compact('admin'))->with(compact('order_level'))->with(compact('count'))->with(compact('level'))->with(compact('groups'));
        }
    }

    // 新建工单处理
    public function postCreateOrder($com)
    {
        // 验证规则
        $rules = array(
            'title' => 'Required',
            'content' => 'Required',
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        if ($validator->passes())
        {
            // 判断是否带附件
            $file_status = 0; // 上传开关
            if (Input::hasFile('file')) {
                $file = Input::file('file');
                foreach ($file as $k => $v) {
                    // 验证附件大小
                    if ($v->getSize() > (2 * 1024 * 1024)) {
                        return Redirect::to($com->path . '/admin/order/createTask')
                        ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                    }
                }

                $file_status = 1;
            }

            // 获取管理员等级
            $level = Config::get('auth.com_admin_level_id');

            // 创建工单
            $order = new Order;
            $config = 0;

            // 组合工单数据
            $order->title = Input::get('title');
            $order->level = Input::get('level');
            $order->type = Input::get('type');
            $order->status = 1;
            $order->reply = Input::get('admin');
            if (Input::get('type') == 'admin') {
                $order->ask = Input::get('adminuser');
                if ($level < 3) {
                    $order->group = DB::table($com->id . '_group_admin_user')
                    ->where('user_id', $order->reply)->pluck('group_id');
                } else {
                    $order->group = DB::table($com->id . '_group_admin_user')
                    ->where('user_id', $order->ask)->pluck('group_id');
                }
            } else {
                // $order->ask = Input::get('user');
                // 暂时处理用于500错误
                // $order->ask = DB::table('users')->where('user_name', Input::get('user_name'))->pluck('id');
                // if ($order->ask == '') {
                //     return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败。不存在的客户！');
                // }

                $member = Input::get('user_name');
                $member_id = DB::table('users')->where('user_name', $member)->pluck('id');
                $group = Input::get('type');
                if ($member_id == '') {
                    // 初始化客户模型类
                    $user = new User;

                    // 组合客户数据
                    $password = '111111';
                    $name = explode("@", $member);
                    $user->user_name = $member;
                    $user->name = $name[0];
                    $user->password  = Hash::make($password);
                    $user->mobile    = '11111111111';
                    $user->head      = 0;
                    
                    // 保存客户数据
                    $config = 0;
                    if ($user->save()) {
                        $userid = $user->id;
                    } else {
                        return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                    }

                    // 初始化客户关系模型
                    $site_user = new SiteUser();

                    // 组合客户关系数据
                    $site_user->company_id   = $com->id;
                    $site_user->user_id      = $userid;
                    $site_user->work_type_id = 1;
                    $site_user->firm = 'email';

                    // 保存客户关系数据
                    if ($site_user->save()) {
                        $config = 0;
                    } else {
                        return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                    }

                    // 初始化客户绑定模型
                    $bind_user = new BindUser();

                    // 组合客户绑定数据
                    $bind_user->group_id = $group;
                    $bind_user->user_id  = $userid;

                    // 保存客户绑定数据
                    if ($bind_user->save()) {
                        $config = 0;
                    } else {
                        return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                    }

                    $order->ask = $userid;
                } else {
                    // 判断是否绑定客户
                    $user_id = DB::table('site_user')->where('user_id', $member_id)
                    ->where('company_id', $com->id)->pluck('user_id');
                    if ($user_id) {
                        $order->ask = $user_id;
                    } else {
                        // 创建新客户
                        $userid = $member_id;

                        // 初始化客户关系模型
                        $site_user = new SiteUser();

                        // 组合客户关系数据
                        $site_user->company_id   = $com->id;
                        $site_user->user_id      = $userid;
                        $site_user->work_type_id = 1;
                        $site_user->firm = 'email';

                        // 保存客户关系数据
                        $config = 0;
                        if ($site_user->save()) {
                            $config = 0;
                        } else {
                            return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                        }

                        // 初始化客户绑定模型
                        $bind_user = new BindUser();

                        // 组合客户绑定数据
                        $bind_user->group_id = $group;
                        $bind_user->user_id  = $userid;

                        // 保存客户绑定数据
                        if ($bind_user->save()) {
                            $order->ask = $userid;
                        } else {
                            return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                        }
                    }
                }

                $order->group = $group;
                $order->email = DB::table($com->id . '_groups')->where('id', $group)->pluck('email_id');
                if ($order->email == '') {
                    return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败，客户所属组别未绑定邮箱！');
                }

                if (DB::table('company_email')->where('disabled', 'true')->where('id', $order->email)->pluck('id')) {
                    return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，客户组别所绑定邮箱未启用！');
                }
            }

            if ($order->save()) {
                // 判断是否需要上传
                $fileID = array();
                if ($file_status == 1) {
                    foreach ($file as $k => $v) {
                        // 获得上传路径
                        $destinationPath = 'uploads/' . $com->byname . '/ask/' . date('Y/m/d/') . Auth::user()->id;

                        // 获取文件类型
                        $mimeType = explode('/', $v->getmimeType());
                        if ($mimeType[0] == 'image') {
                            $file_type = $mimeType[0];
                        } else {
                            $file_type = 'file';
                        }
                        
                        // 文件上传处理
                        $extension = $v->getClientOriginalExtension();
                        $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                        $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                        $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                        $start_name = rtrim($body_name[0], '.'); // 获取名称
                        $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;
                        $upload_success = $v->move($destinationPath, $ClientOriginalName);
                        if ($upload_success) {
                            $encl = new Enclosure();
                            $encl->enclosure_name = $v->getClientOriginalName();
                            $encl->type = $file_type;
                            $encl->suffix = $extension;
                            $encl->path = $destinationPath . '/' . $ClientOriginalName;
                            $encl->ask = Auth::user()->id;
                            $encl->order = $order->id;

                            if ($encl->save()) {
                                $fileID[] = $encl->id;
                            }
                        }
                    }
                }

                // 定义附件参数
                $reply_enclosure = '';
                if (count($fileID) > 0) {
                    foreach ($fileID as $k) {
                        $reply_enclosure .= $k . ',';
                    }

                    $reply_enclosure = rtrim($reply_enclosure, ',');
                }

                // 创建提问记录
                $ask = new OrderAsk;

                // 组合提问记录数据
                $ask->order = $order->id;
                $ask->ask = $order->ask;
                // $ask->content = str_replace("\r\n", "<br>", Input::get('content'));
                $ask->content = Input::get('content');
                if (Input::get('type') == 'user') {
                    $ask->email = $order->email;
                }

                $ask->enclosure = $reply_enclosure;
                if ($reply_enclosure != '') {
                    $ask->attachment = 2;
                }
                
                // 保存提问数据
                if ($ask->save()) {
                    $config = 0;

                    // 判断是否带备注内容
                    if (Input::get('remark_content') != '') {
                        $remark_content = str_replace("\r\n", "<br>", Input::get('remark_content'));

                        // 创建工单备注对象
                        $remark = new OrderRemark;
                        
                        // 组合工单备注数据
                        $remark->order_ask = $ask->id;
                        $remark->order = $order->id;
                        $remark->admin = Auth::user()->id;
                        $remark->content = $remark_content;

                        // 保存工单备注数据
                        $remark->save();
                    }

                    // 创建工单日志对象
                    $log = new OrderLog;

                    // 组合工单日志数据
                    $log->order = $order->id;
                    $log->admin = Auth::user()->id;
                    $log->people = Input::get('admin');
                    $log->content = '创建内部工单并将工单分配给';

                    // 保存工单日志
                    if ($log->save()) {
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
            
            if ($config == 1) {
                return Redirect::to($com->path . '/admin/order/createOrder')->with('error', '创建工单失败！');
            } else {
                return Redirect::to($com->path . '/admin/order/createOrder')->with('success', '创建工单成功！');
            }
        }

        // 返回错误信息
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    // 新建任务
    public function getCreateTask($com)
    {
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单等级
        $order_level = DB::table($com->id . '_order_level')->where('disabled', '=', 'false')->get();
        
        // 工单总数
        $count = $this->orderCount($com, $level);

        // 获取组信息
        if ($level > 2) {
            $group = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            $groups = Group::whereIn('id', $group)->where('email_id', '>', 0)->get();
        } elseif ($level < 3) {
            $groups = Group::where('email_id', '>', 0)->get();
        }

        // 去除未启用邮箱的组别
        foreach ($groups as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($groups[$k]);
            }
        }

        // 新建工单标识
        $os = 8;
        $admin = 0; // 暂时不需要后台权限数据

        // 加载模版
        $user = 0; // 暂时处理用于500错误
        return View::make('com/admin/order/createTask')->with('com', $com)->with(compact('user'))->with(compact('os'))
        ->with(compact('admin'))->with(compact('order_level'))->with(compact('count'))->with(compact('level'))->with(compact('groups'));
    }

    // 新建任务处理
    public function postCreateTask($com)
    {
        // 判断是否带附件
        $file_status = 0; // 上传开关
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            foreach ($file as $k => $v) {
                // 验证附件大小
                if ($v->getSize() > (2 * 1024 * 1024)) {
                    return Redirect::to($com->path . '/admin/order/createTask')
                    ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                }
            }

            $file_status = 1;
        }

        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');
        if (Input::get('email') != '') {
            $member = Input::get('email');
            $status = 1;
        } else {
            $member = Input::get('user');
            $status = 0;
        }

        // 判断是否存在客户
        $member_id = DB::table('users')->where('user_name', $member)->pluck('id');

        // 判断是否需要创建新客户
        if ($status == 1) {
            if ($member_id) {
                $user_id = DB::table('site_user')->where('user_id', $member_id)
                ->where('company_id', $com->id)->pluck('user_id');

                // 判断是否绑定客户
                if ($user_id) {
                    // binway需求都可创建消息
                    $userID = $member_id;
                    $groupID = Input::get('group');
                } else {
                    // 创建新客户
                    $userid = $member_id;

                    // 初始化客户关系模型
                    $site_user = new SiteUser();

                    // 组合客户关系数据
                    $site_user->company_id   = $com->id;
                    $site_user->user_id      = $userid;
                    $site_user->work_type_id = 1;
                    $site_user->firm = 'email';

                    // 保存客户关系数据
                    $config = 0;
                    if ($site_user->save()) {
                        $config = 0;
                    } else {
                        return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                    }

                    // 初始化客户绑定模型
                    $bind_user = new BindUser();

                    // 组合客户绑定数据
                    $bind_user->group_id = Input::get('group');
                    $bind_user->user_id  = $userid;

                    // 保存客户绑定数据
                    if ($bind_user->save()) {
                        $config = 0;
                    } else {
                        return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                    }

                    $userID = $userid;
                    $groupID = Input::get('group');
                }
            } else {
                // 创建新客户
                // 初始化客户模型类
                $user = new User;

                // 组合客户数据
                $password = '111111';
                $name = explode("@", $member);
                $user->user_name = $member;
                $user->name = $name[0];
                $user->password  = Hash::make($password);
                $user->mobile    = '11111111111';
                $user->head      = 0;
                
                // 保存客户数据
                $config = 0;
                if ($user->save()) {
                    $userid = $user->id;
                } else {
                    return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                }

                // 初始化客户关系模型
                $site_user = new SiteUser();

                // 组合客户关系数据
                $site_user->company_id   = $com->id;
                $site_user->user_id      = $userid;
                $site_user->work_type_id = 1;
                $site_user->firm = 'email';

                // 保存客户关系数据
                if ($site_user->save()) {
                    $config = 0;
                } else {
                    return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                }

                // 初始化客户绑定模型
                $bind_user = new BindUser();

                // 组合客户绑定数据
                $bind_user->group_id = Input::get('group');
                $bind_user->user_id  = $userid;

                // 保存客户绑定数据
                if ($bind_user->save()) {
                    $config = 0;
                } else {
                    return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，收件人 '.$member.' 创建失败！');
                }

                $userID = $userid;
                $groupID = Input::get('group');
            }
        } else {
            $groupID = Input::get('group');
            $userID = $member_id;
        }

        // 判断回复内容是否需要审核
        $system = 0; // 审核开关
        $check = DB::table($com->id . '_groups')->where('id', $groupID)->pluck('check');
        if ($check == '1') { // 需要审核
            if ($level == 4) {
                $admin_check = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('check');
                if ($admin_check == '1') { // 判断客服是否设置为需要审核
                    $system = 1;
                }
            }
        }

        // 获取表单数据
        $data = array(
            'title' => Input::get('title'),
            // 'content' => str_replace("\r\n", "<br>", Input::get('content')),
            'content' => Input::get('content'),
            'ask' => $userID,
            'group' => $groupID,
            'reply' => Auth::user()->id,
            'system' => $system,
        );

        // 创建工单
        $email_id = DB::table($com->id . '_groups')->where('id', $data['group'])->pluck('email_id');
        if ($email_id == '') {
            return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，选择的组别未绑定邮箱！');
        }

        if (DB::table('company_email')->where('disabled', 'true')->where('id', $email_id)->pluck('id')) {
            return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败，组别所绑定邮箱未启用！');
        }

        $order = new Order();

        // 组合工单数据
        $order->title = $data['title'];
        $order->ask = $data['ask'];
        $order->group = $data['group'];
        $order->reply = $data['reply'];
        $order->email = $email_id;
        $order->status = 2;

        // 保存工单数据
        if ($order->save()) {
            $order_id = $order->id;
        }

        // 判断是否需要上传
        $fileID = array();
        if ($file_status == 1) {
            foreach ($file as $k => $v) {
                // 获得上传路径
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;

                // 获取文件类型
                $mimeType = explode('/', $v->getmimeType());
                if ($mimeType[0] == 'image') {
                    $file_type = $mimeType[0];
                } else {
                    $file_type = 'file';
                }
                
                // 文件上传处理
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;
                $extension = $v->getClientOriginalExtension();

                $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;

                $upload_success = $v->move($destinationPath, $ClientOriginalName);
                if ($upload_success) {
                    $encl = new Enclosure();
                    $encl->enclosure_name = $v->getClientOriginalName();
                    $encl->type = $file_type;
                    $encl->suffix = $extension;
                    $encl->path = $destinationPath . '/' . $ClientOriginalName;
                    $encl->reply = Auth::user()->id;
                    $encl->order = $order_id;

                    if ($encl->save()) {
                        $fileID[] = $encl->id;
                    }
                }
            }
        }

        // 定义附件参数
        $reply_enclosure = '';
        if (count($fileID) > 0) {
            foreach ($fileID as $k) {
                $reply_enclosure .= $k . ',';
            }

            $reply_enclosure = rtrim($reply_enclosure, ',');
        }

        // 创建回答对象
        $reply = new OrderReply;
        
        // 组合回答数据
        $reply->order = $order_id;
        $reply->reply = $data['reply'];
        $reply->content = $data['content'];
        $reply->system = $data['system'];
        $reply->enclosure = $reply_enclosure;
        $reply->email = $email_id;

        // 保存回答数据
        if ($reply->save()) {
            $reply_id = $reply->id;

            // 判断是否带备注内容
            if (Input::get('remark_content') != '') {
                $remark_content = str_replace("\r\n", "<br>", Input::get('remark_content'));

                // 创建工单备注对象
                $remark = new OrderRemark;
                
                // 组合工单备注数据
                $remark->order_reply = $reply_id;
                $remark->order = $order_id;
                $remark->admin = Auth::user()->id;
                $remark->content = $remark_content;

                // 保存工单备注数据
                $remark->save();
            }
        }

        $e_config = 0;
        if ($email_id > 0) {
            $from_email = DB::table('company_email')->where('id', $email_id)->pluck('email');
            $cc = rtrim(Input::get('cc'), ',');

            // 创建队列对象
            $quere = new OrderQuere;

            // 组合队列数据
            $quere->company_id = $com->id;
            $quere->byname = $com->byname;
            $quere->order = $order_id;
            $quere->reply = $reply->id;
            $quere->title = '#'.$order_id.': '.$data['title'];
            $quere->from_email = $from_email;
            $quere->to_email = DB::table('users')->where('id', $data['ask'])->pluck('user_name');
            $quere->content = $data['content'];
            $quere->process = $data['system'];
            $quere->enclosure = $reply_enclosure;
            if ($cc != '') {
                $quere->cc = $cc;
            }

            // 保存队列数据
            if ($quere->save()) {
                $e_config = 0;
            } else {
                $e_config = 1;
            }

            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order_id;
            $log->admin = Auth::user()->id;
            $log->content = '创建工单并发送回复消息';

            // 保存工单日志
            if ($log->save()) {
                $e_config = 0;
            } else {
                $e_config = 1;
            }
        }

        // 加载模板
        if ($e_config == 0) {
            return Redirect::to($com->path . '/admin/order/createTask')->with('success', '创建工单成功！');
        } else {
            return Redirect::to($com->path . '/admin/order/createTask')->with('error', '创建工单失败！');
        }
    }

    // 工单日志
    public function getOrderLog($com)
    {
        // 获取工单日志记录
        $status = $_GET['status'];
        $id = OrderLog::where('order', $_GET['id'])->get()->lists('id');
        if (count($id) == 0) {
            $order = 0;
            $log = 0;
        } else {
            $log = DB::table($com->id . '_order_log')->whereIN($com->id . '_order_log.id', $id)->orderBy($com->id . '_order_log.updated_at', 'desc')->get();
            $order = DB::table($com->id . '_order')->where('id', $_GET['id'])->first();

            // 获取用户信息
            foreach ($log as $k => $v) {
                $admin = DB::table('users')->where('id', $v->admin)->first();
                $log[$k]->name = $admin->name;
                $log[$k]->user_name = $admin->user_name;
                if ($admin->head > 0) {
                    $log[$k]->head_path = DB::table('enclosures')->where('id', $admin->head)->pluck('path');
                }

                $log[$k]->log_content = $log[$k]->name.' '.$log[$k]->content;
                if ($v->people > 0) {
                    $people = DB::table('users')->where('id', $v->people)->first();
                    $log[$k]->log_content .= ' '.$people->name;
                }
            }
        }
        
        return View::make('com/admin/order/log')->with('com', $com)->with(compact('log'))
        ->with(compact('order'))->with(compact('status'));
    }

    // 邮件抄送
    public function getCCEmail($com)
    {
        $id = $_GET['id'];
        $status = $_GET['status'];

        return View::make('com/admin/order/ccemail')->with('com', $com)
        ->with(compact('id'))->with(compact('status'));
    }

    // js escape解码
    function unescape($str) 
    { 
        $ret = ''; 
        $len = strlen($str); 

        for ($i = 0; $i < $len; $i++) { 
            if ($str[$i] == '%' && $str[$i+1] == 'u') { 
                $val = hexdec(substr($str, $i+2, 4)); 
                if ($val < 0x7f) $ret .= chr($val); 
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
                $i += 5; 
            } elseif ($str[$i] == '%'){ 
                $ret .= urldecode(substr($str, $i, 3)); 
                $i += 2; 
            } else $ret .= $str[$i]; 
        } 

        return $ret; 
    }
}
