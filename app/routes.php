<?php
// 路由设置

// 工单邮件内容发送脚本
Route::get('api/v1/contentquere', 'QuereEmailController@contentQuere');
// 工单邮件统计发送脚本
Route::get('api/v1/countquere', 'QuereEmailController@countQuere');
// 工单邮件接收脚本
Route::get('api/v1/imapwork', 'RequestEmailController@imapWork');

// binway工单邮件定制 - no - start
// 工单邮件接收脚本
Route::get('api/v1/emailresponse', 'QuereEmailController@emailResponse');
// 工单垃圾邮件接收脚本
Route::get('api/v1/emailspamresponse', 'QuereEmailController@emailSpamResponse');
// 工单邮件附件接收脚本
Route::get('api/v1/emailattachment', 'QuereEmailController@emailAttachment');
// 工单邮件发送脚本
Route::get('api/v1/emailrequest', 'QuereEmailController@emailRequest');
// ecstore会员同步接口
Route::get('api/v1/user', 'QuereEmailController@emailUser');
// binway工单邮件定制 - no - end

// 整站
//
// Route::get('/', 'SiteHomeController@index');
Route::get('/', function()
{
    return Redirect::to('no/admin');
});

// 超级管理员
//
Route::group(array('prefix' => 'root'), function ()
{
    // 首页
    //
    Route::get('/', 'RootDashboardController@index');

    // 个人资料
    //
    Route::get('account', 'RootAccountController@getIndex');
    Route::post('account', 'RootAccountController@postIndex');

    // 登录
    //
    Route::get('login', 'RootAccountController@getLogin');
    Route::post('login', 'RootAccountController@postLogin');

    // 退出
    //
    Route::get('logout', 'RootAccountController@getLogout');

    // 公司管理
    // 首页
    Route::get('company', 'RootCompanyController@index');
    // 公司创建
    Route::get('company/create', 'RootCompanyController@getCreate');
    Route::post('company/create', 'RootCompanyController@postCreate');
    // 公司修改
    Route::get('company/edit/{companyId}', 'RootCompanyController@getEdit');
    Route::post('company/edit/{companyId}', 'RootCompanyController@postEdit');
    // 禁用公司
    Route::get('company/disabled/{companyId}', 'RootCompanyController@getDisabled');
    // 启用公司
    Route::get('company/enable/{companyId}', 'RootCompanyController@getEnable');

    // 用户管理
    // 首页
    Route::get('user', 'RootUserController@index');
    // 禁用用户
    Route::get('user/disabled/{userId}', 'RootUserController@getDisabled');
    // 启用用户
    Route::get('user/enable/{userId}', 'RootUserController@getEnable');
    // 查看用户信息
    Route::get('user/view/{userId}', 'RootUserController@getView');
    // 更改用户密码
    Route::get('user/changepassword/{userId}', 'RootUserController@getChangePassword');
    // 更改用户密码处理
    Route::post('user/changepassword/{userId}', 'RootUserController@postChangePassword');

    // 报表统计
    // 首页
    Route::get('report', 'RootReportController@index');

    // 系统设置
    // 首页
    Route::get('config', 'RootConfigController@index');

});


// 公司
//
Route::group(array('prefix' => '{com}'), function ()
{
    // 首页
    //
    Route::get('/', 'ComSiteContronller@index');

    // 登录
    //
    Route::get('login', 'ComSiteAccountContronller@getLogin');
    Route::post('login', 'ComSiteAccountContronller@postLogin');

    // 退出
    //
    Route::get('logout', 'ComSiteAccountContronller@getLogout');

    // 个人资料
    // no
    Route::get('account', 'ComSiteAccountContronller@getIndex');
    Route::post('account', 'ComSiteAccountContronller@postIndex');

    // 应用设置
    // no
    Route::get('apply', 'ComSiteApplyContronller@getIndex');
    Route::post('apply', 'ComSiteApplyContronller@postIndex');

    // 客户
    // no
    Route::group(array('prefix' => 'site'), function ()
    {
        // 工单管理
        // 首页(待受理工单列表)
        Route::get('work', 'ComSiteWorkController@getWork');
        // 提交工单
        Route::get('work/job', 'ComSiteWorkController@getJobWork');
        // 提交工单处理
        Route::post('work/job', 'ComSiteWorkController@postJobWork');
        // 处理中工单列表
        Route::get('work/untreated', 'ComSiteWorkController@getUntreated');
        // 已处理工单列表
        Route::get('work/processed', 'ComSiteWorkController@getProcessed');
        // 已关闭工单列表
        Route::get('work/closed', 'ComSiteWorkController@getClosed');
        // 工单详情页
        Route::get('work/workdetail/{workId}', 'ComSiteWorkController@workDetail');
        // 工单详情页回复处理
        Route::post('work/workdetail/{workId}', 'ComSiteWorkController@workProcessed');
        // 重启(已处理)工单
        Route::get('work/workprocessed/{workId}', 'ComSiteWorkController@workAllot');
        // 关闭工单
        Route::get('work/workclosed/{workId}', 'ComSiteWorkController@workClosed');
        // 工单标题搜索
        Route::get('work/searchwork/{status}/{title}', 'ComSiteWorkController@searchWork');
        // 工单往来附件记录
        Route::get('work/enclosurework/{workId}', 'ComSiteWorkController@enclosureWork');
        // 工单消息通知
        Route::post('work/messagework', 'ComSiteWorkController@messageWork');
        // 工单消息通知详情
        Route::get('work/getmessage', 'ComSiteWorkController@getMessage');
    });

    // 管理员
    //
    Route::group(array('prefix' => 'admin'), function ()
    {
        // 首页
        //
        Route::get('/', 'ComAdminDashboardController@index');

        // 个人资料
        //
        Route::get('account', 'ComAdminAccountController@getIndex');
        Route::post('account', 'ComAdminAccountController@postIndex');

        // 应用设置
        //
        Route::get('apply', 'ComAdminApplyContronller@getIndex');
        Route::post('apply', 'ComAdminApplyContronller@postIndex');

        // 登录
        //
        Route::get('login', 'ComAdminAccountController@getLogin');
        Route::post('login', 'ComAdminAccountController@postLogin');

        // 退出
        //
        Route::get('logout', 'ComAdminAccountController@getLogout');

        // 工单管理 - no
        // 首页
        Route::get('work', 'ComAdminWorkController@index');
        // 未处理工单列表
        Route::get('work/untreated/{level}', 'ComAdminWorkController@getUntreated');
        // 已处理工单列表
        Route::get('work/processed/{level}', 'ComAdminWorkController@getProcessed');
        // 已关闭工单列表
        Route::get('work/closed/{level}', 'ComAdminWorkController@getClosed');
        // 工单详情页
        Route::get('work/workdetail/{workId}', 'ComAdminWorkController@workDetail');
        // 工单详情页回复处理
        Route::post('work/workdetail/{workId}', 'ComAdminWorkController@workProcessed');
        // 工单完成处理
        Route::get('work/workachieve/{workId}', 'ComAdminWorkController@workAchieve');
        // 认领工单
        Route::get('work/workclaim/{workId}', 'ComAdminWorkController@workClaim');
        // 关闭工单
        Route::get('work/workclosed/{workId}', 'ComAdminWorkController@workClosed');
        // 工单标题搜索
        Route::get('work/searchwork/{status}/{title}', 'ComAdminWorkController@searchWork');
        // 工单标题搜索(查询组员工单)
        Route::get('work/searchgroupwork/{status}/{title}', 'ComAdminWorkController@searchGroupWork');
        // 工单分配
        Route::get('work/allot/{workId}', 'ComAdminWorkController@getAllot');
        // 工单分配处理
        Route::get('work/allotmanage/{workId}', 'ComAdminWorkController@allotManage');
        // 重启(已处理)工单
        Route::get('work/allotmanageoriginal/{workId}', 'ComAdminWorkController@allotManageOriginal');
        // 工单往来附件记录
        Route::get('work/enclosurework/{workId}', 'ComAdminWorkController@enclosureWork');
        // 工单邀请
        Route::get('work/dispatch/{workId}', 'ComAdminWorkController@getDispatch');
        // 工单邀请处理
        Route::get('work/dispatchmanage/{workId}', 'ComAdminWorkController@dispatchManage');
        // 工单邀请列表
        Route::get('work/invite/{level}', 'ComAdminWorkController@getInvite');
        // 工单邀请详情页
        Route::get('work/workinvite/{workId}', 'ComAdminWorkController@workDetailInvite');
        // 工单邀请详情页回复处理
        Route::post('work/workinvite/{workId}', 'ComAdminWorkController@workProcessedInvite');
        // 工单邀请标题搜索
        Route::get('work/searchinvitework/{status}/{title}', 'ComAdminWorkController@searchInviteWork');
        // 工单审核首页
        Route::get('check', 'ComAdminCheckController@index');
        // 审核工单历史查看
        Route::get('check/checkdetail/{workId}/{replyId}', 'ComAdminCheckController@checkReplyDetail');
        // 工单审核
        Route::get('check/checkreply/{workId}/{replyId}', 'ComAdminCheckController@checkReplyGet');
        // 工单审核处理
        Route::get('check/checkprocess/{workId}/{replyId}/{adminId}', 'ComAdminCheckController@checkProcess');
        // 工单审核建议
        Route::get('check/checklog/{workId}/{replyId}', 'ComAdminCheckController@checkLog');
        // 工单消息通知
        Route::post('work/messagework', 'ComAdminWorkController@messageWork');
        // 工单消息通知详情
        Route::get('work/getmessage', 'ComAdminWorkController@getMessage');

        // 客户管理
        // 首页
        Route::get('client', 'ComAdminClientController@index');
        // 创建客户
        Route::get('client/create', 'ComAdminClientController@getCreate');
        // 创建客户处理
        Route::post('client/create', 'ComAdminClientController@postCreate');
        // 禁用客户
        Route::get('client/disabled/{clientId}', 'ComAdminClientController@getDisabled');
        // 启用客户
        Route::get('client/enable/{clientId}', 'ComAdminClientController@getEnable');
        // 查看客户信息
        Route::get('client/view/{clientId}', 'ComAdminClientController@getView');
        // 绑定客户信息
        Route::get('client/bind/{clientId}', 'ComAdminClientController@getBind');
        // 绑定客户处理
        Route::get('client/bindmanage/{clientId}', 'ComAdminClientController@bindManage');
        // 更改客户密码
        Route::get('client/changepassword/{clientId}', 'ComAdminClientController@getChangePassword');
        // 更改客户密码处理
        Route::post('client/changepassword/{clientId}', 'ComAdminClientController@postChangePassword');
        // 客户导入
        Route::get('client/importclient', 'ComAdminClientController@getImportClient');
        // 客户导入处理
        Route::post('client/importclient', 'ComAdminClientController@postImportClient');
        // 客户分页设置
        Route::post('client/page', 'ComAdminClientController@page');
        // 向客户发送邮件
        Route::get('client/email/{clientId}', 'ComAdminClientController@getClientEmail');
        // 向客户发送邮件处理
        Route::post('client/email/{clientId}', 'ComAdminClientController@postClientEmail');
    
        // 用户管理
        // 首页
        Route::get('user', 'ComAdminUserController@index');
        // 公司管理员列表
        Route::get('user/level/admin', 'ComAdminUserController@getLevelAdmin');
        // 公司管理员创建
        Route::get('user/level/admin/create', 'ComAdminUserController@getLevelAdminCreate');
        // 公司管理员创建处理
        Route::post('user/level/admin/create', 'ComAdminUserController@postLevelAdminCreate');
        // 编辑管理员
        Route::get('user/level/admin/edit/{userId}', 'ComAdminUserController@getLevelAdminEdit');
        // 编辑管理员处理
        Route::post('user/level/admin/edit/{userId}', 'ComAdminUserController@postLevelAdminEdit');
        // 禁用管理员
        Route::get('user/level/admin/disabled/{userId}', 'ComAdminUserController@getLevelAdminDisabled');
        // 启用管理员
        Route::get('user/level/admin/enable/{userId}', 'ComAdminUserController@getLevelAdminEnable');

        // 客服列表
        Route::get('user/level/customer', 'ComAdminUserController@getLevelCustomer');
        // 客服创建
        Route::get('user/level/customer/create', 'ComAdminUserController@getLevelCustomerCreate');
        // 客服创建处理
        Route::post('user/level/customer/create', 'ComAdminUserController@postLevelCustomerCreate');
        // 客服修改
        Route::get('user/level/customer/edit/{userId}', 'ComAdminUserController@getLevelCustomerEdit');
        // 客服修改处理
        Route::post('user/level/customer/edit/{userId}', 'ComAdminUserController@postLevelCustomerEdit');
        // 禁用客服
        Route::get('user/level/customer/disabled/{userId}', 'ComAdminUserController@getLevelCustomerDisabled');
        // 启用客服
        Route::get('user/level/customer/enable/{userId}', 'ComAdminUserController@getLevelCustomerEnable');
        // 查看客服信息
        Route::get('user/level/customer/view/{userId}', 'ComAdminUserController@getLevelCustomerView');


        // 组别管理
        // 首页
        Route::get('group', 'ComAdminGroupController@index');
        // 创建
        Route::get('group/create', 'ComAdminGroupController@getCreate');
        // 创建处理
        Route::post('group/create', 'ComAdminGroupController@postCreate');
        // 修改
        Route::get('group/edit/{groupId}', 'ComAdminGroupController@getEdit');
        // 修改处理
        Route::post('group/edit/{groupId}', 'ComAdminGroupController@postEdit');


        // 报表统计
        // 首页(工单报表)
        Route::get('report', 'ComAdminReportController@index');
        // 客户报表
        Route::get('report/user', 'ComAdminReportController@user');
        // 用户报表
        Route::get('report/admin', 'ComAdminReportController@admin');

        // 邮件队列
        // 首页
        Route::get('quere', 'ComAdminQuereController@index');
        // 用户邮件队列
        Route::get('quere/reply', 'ComAdminQuereController@reply');
        // 客户邮件队列
        Route::get('quere/ask', 'ComAdminQuereController@ask');
        // 邮件统计队列
        Route::get('quere/emailcount', 'ComAdminQuereController@emailCount');
        // 用户查看队列
        Route::get('quere/reply/view/{replyId}', 'ComAdminQuereController@viewReply');
        // 客户查看队列
        Route::get('quere/ask/view/{askId}', 'ComAdminQuereController@viewAsk');
        // 用户手动队列
        Route::get('quere/reply/execute/{quereid}', 'ComAdminQuereController@executeReply');
        // 客户手动队列
        Route::get('quere/ask/execute/{quereid}', 'ComAdminQuereController@executeAsk');
        
        // 系统设置
        // 首页
        Route::get('config', 'ComAdminConfigController@index');
        // 业务形态列表
        Route::get('config/types', 'ComAdminConfigController@getTypes');
        // 业务形态创建
        Route::get('config/type/create', 'ComAdminConfigController@getTypeCreate');
        // 业务形态创建处理
        Route::post('config/type/create', 'ComAdminConfigController@postTypeCreate');
        // 业务形态修改
        Route::get('config/type/edit/{typeId}', 'ComAdminConfigController@getTypeEdit');
        // 业务形态修改处理
        Route::post('config/type/edit/{typeId}', 'ComAdminConfigController@postTypeEdit');
        // 邮箱配置
        Route::get('config/mail', 'ComAdminConfigController@getMail');
        // 邮箱创建
        Route::get('config/mail/create', 'ComAdminConfigController@getCreateMail');
        // 邮箱创建处理
        Route::post('config/mail/create', 'ComAdminConfigController@postCreateMail');
        // 邮箱配置查看
        Route::get('config/mail/view/{emailId}', 'ComAdminConfigController@getviewMail');
        // 邮箱配置处理
        Route::post('config/mail/view/{emailId}', 'ComAdminConfigController@postViewMail');
        // 邮箱组别绑定
        Route::get('config/mail/group', 'ComAdminConfigController@getGroup');
        // 邮箱组别绑定处理
        Route::post('config/mail/group', 'ComAdminConfigController@postGroup');
        // 公司首页挂件及logo配置
        Route::get('config/pendant', 'ComAdminConfigController@getPendant');
        // 公司首页挂件及logo配置处理
        Route::post('config/pendant', 'ComAdminConfigController@postPendant');
        // 工单级别列表
        Route::get('config/orderLevel', 'ComAdminConfigController@getOrderLevel');
        // 工单级别创建
        Route::get('config/orderLevel/create', 'ComAdminConfigController@getOrderLevelCreate');
        // 工单级别创建处理
        Route::post('config/orderLevel/create', 'ComAdminConfigController@postOrderLevelCreate');
        // 工单级别修改
        Route::get('config/orderLevel/edit/{level_id}', 'ComAdminConfigController@getOrderLevelEdit');
        // 工单级别修改处理
        Route::post('config/orderLevel/edit/{level_id}', 'ComAdminConfigController@postOrderLevelEdit');
        // 工单级别使用停用
        Route::get('config/orderLevel/update/{level_id}', 'ComAdminConfigController@getOrderLevelUpdate');

        // binway工单定制 - no - start
        // 工单首页
        Route::get('order', 'ComAdminOrderController@index');
        // 新建工单
        Route::get('order/createOrder', 'ComAdminOrderController@getCreateOrder');
        // 新建工单处理
        Route::post('order/createOrder', 'ComAdminOrderController@postCreateOrder');
        // 新建任务
        Route::get('order/createTask', 'ComAdminOrderController@getCreateTask');
        // 新建任务处理
        Route::post('order/createTask', 'ComAdminOrderController@postCreateTask');
        // 工单内容ajax
        Route::post('order/orderContent', 'ComAdminOrderController@getOrderContent');
        // 工单分配
        Route::get('order/orderallot', 'ComAdminOrderController@getOrderAllot');
        // 工单分配处理
        Route::get('order/orderallot/create', 'ComAdminOrderController@postOrderAllot');
        // 工单转移
        Route::get('order/ordermigrate', 'ComAdminOrderController@getOrderMigrate');
        // 工单转移处理
        Route::get('order/ordermigrate/create', 'ComAdminOrderController@postOrderMigrate');
        // 工单邀请
        Route::get('order/orderinvite', 'ComAdminOrderController@getOrderInvite');
        // 工单邀请处理
        Route::get('order/orderinvite/create', 'ComAdminOrderController@postOrderInvite');
        // 将工单扔进废纸篓
        Route::get('order/orderdisabled', 'ComAdminOrderController@postOrderDisabled');
        // 将工单移出废纸篓
        Route::get('order/orderdisabledout', 'ComAdminOrderController@postOrderDisabledOut');
        // 标记银行凭证
        Route::get('order/orderbank', 'ComAdminOrderController@postOrderBank');
        // 自定义标签
        Route::get('order/ordertab', 'ComAdminOrderController@getOrderTab');
        // 自定义标签处理
        Route::get('order/ordertab/create', 'ComAdminOrderController@postOrderTab');
        // 变更等级
        Route::get('order/orderleveledit', 'ComAdminOrderController@getOrderLevelEdit');
        // 变更等级处理
        Route::get('order/orderleveledit/create', 'ComAdminOrderController@postOrderLevelEdit');
        // 工单往来附件
        Route::get('order/order_enclosure', 'ComAdminOrderController@getOrderEnclosure');
        // 工单回复处理
        // Route::post('order/orderReply', 'ComAdminOrderController@postOrderReply');
        Route::post('order', 'ComAdminOrderController@postOrderReply');
        // 工单审核
        Route::get('order/process', 'ComAdminOrderController@getProcess');
        // 工单审核处理
        Route::get('order/process/create', 'ComAdminOrderController@postProcess');
        // 工单驳回
        Route::get('order/overrule', 'ComAdminOrderController@getOverrule');
        // 工单驳回处理
        Route::post('order/overrule/create', 'ComAdminOrderController@postOverrule');
        // 备注信息
        Route::get('order/comment', 'ComAdminOrderController@getComment');
        // 附件信息
        Route::get('order/enclosure', 'ComAdminOrderController@getEnclosure');
        // 工单备注
        Route::get('order/remark', 'ComAdminOrderController@getRemark');
        // 工单备注处理
        Route::get('order/remark/create', 'ComAdminOrderController@postRemark');
        // // 工单回复追加备注
        // Route::get('order/replyremark', 'ComAdminOrderController@getReplyRemark');
        // // 工单回复追加备注处理
        // Route::get('order/replyremark/create', 'ComAdminOrderController@postReplyRemark');
        // 工单草稿存储
        Route::get('order/draft/create', 'ComAdminOrderController@postDraftCreate');
        // 工单草稿删除
        Route::get('order/draft/delete', 'ComAdminOrderController@postDraftDelete');
        // 工单批量操作
        Route::get('order/order_filter', 'ComAdminOrderController@getFilter');
        // 工单批量分配处理
        Route::get('order/orderallotall/create', 'ComAdminOrderController@postOrderAllotAll');
        // 工单批量转移处理
        Route::get('order/ordermigrateall/create', 'ComAdminOrderController@postOrderMigrateAll');
        // 工单日志
        Route::get('order/orderlog', 'ComAdminOrderController@getOrderLog');
        // 邮件队列
        Route::get('quere/order', 'ComAdminQuereController@getOrderQuere');
        // 点击显示更多
        Route::post('order/orderMore', 'ComAdminOrderController@getOrderTitle');

        include 'routes/groupsending.php';
        // binway工单定制 - no - end
    });
});

// 绑定公司参数
//
Route::bind('com', function ($value, $route)
{
    // 查询数据公司 （查询信息缓存60分钟）
    //
    $com = Company::where('path', $value)->where('disabled', 'false')->remember(60)->first();

    // 判断是否存在公司记录
    //
    if ($com)
    {
        // 设置公司ID
        //
        Config::set('app.com_id', $com->id);

        // 设置公司path
        //
        Config::set('app.com_path', $com->path);

        // 获取公司邮箱
        //
        $com_email = CompanyEmail::where('company_id', $com->id)->first();

        // 设置发信邮箱
        //
        if ($com_email)
        {
            Config::set('mail.from.address', $com_email->email);
        }
    }
    else
    {
        // 不存在直接 404
        App::error(function($exception, $code)
        {
            switch ($code)
            {
                // case 403:
                //     return Response::view('errors.403', array(), 403);

                // case 404:
                //     return Response::view('errors.404', array(), 404);

                // case 500:
                //     return Response::view('errors.500', array(), 500);

                // default:
                //     return Response::view('errors.default', array(), $code);
            }
        });
    }

    // 返回公司信息
    //
    return $com;
});

App::error(function($exception, $code)
{
    switch ($code)
    {
        // case 403:
        //     return Response::view('errors.403', array(), 403);

        // case 404:
        //     return Response::view('errors.404', array(), 404);

        // case 500:
        //     return Response::view('errors.500', array(), 500);

        // default:
        //     return Response::view('errors.default', array(), $code);
    }
});