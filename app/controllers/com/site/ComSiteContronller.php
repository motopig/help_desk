<?php

/**
 * Class ComSiteContronller
 *
 * 前台主控制器 by cooper
 */
class ComSiteContronller extends BaseController {

    // 不检查权限方法
    protected $whitelist = array(
        'index'
    );

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();

        $this->beforeFilter('com.site.auth', array('except' => $this->whitelist));

        // 验证是否为前台客户登入 - no
        if (Auth::check()) {
            Auth::user()->firm = DB::table('site_user')->where('user_id', Auth::user()->id)->pluck('firm');
            
            // 获取头像 - no
            if (Auth::user()->head > 0) {
                Auth::user()->enclosures_path = DB::table('enclosures')->where('id', Auth::user()->head)->pluck('path');
            } else {
                Auth::user()->enclosures_path = 0;
            }

            // 获取消息通知及提示音配置 － no
            Auth::user()->message = DB::table('site_user')->where('user_id', Auth::user()->id)->pluck('message');
            Auth::user()->audio = DB::table('site_user')->where('user_id', Auth::user()->id)->pluck('audio');
        }
    }

    /**
     * 前台首页
     *
     * @param $com 公司
     *
     * @return mixed
     */
    public function index($com)
    {
        $logos = $this->logoPendant($com);
        $pendantData = $this->imagePendant($com);
        
        if ($com->path == 'no') {
            return View::make('com/site/no')->with('com', $com);
        } else {
            return View::make('com/site/index')->with('com', $com)->with(compact('logos'))->with(compact('pendantData'));
        }
    }

    // 获取首页logo
    public function logoPendant($com)
    {
        $comData = DB::table('companys')->where('id', $com->id)->first(); // logo及时显示需要先取，后期优化缓存 by no
        // $comData = $com; // 传参和查询结果是等同的，直接赋值！ by Cooper
        $comData->logopath = DB::table('enclosures')->where('id', $comData->logo)->pluck('path');
        if ($comData->logopath) {
            $logos['url'] = $comData->logopath;
        } else {
            // $logos = array(); // 初始化为数组 by Cooper
            $logos['url'] = ''; // 前台logo获取图片路径时需要获取 by no
        }
        $logos['company_name'] = $comData->company_name;
        $logos['brief'] = $comData->brief;

        return $logos;
    }

    // 获取首页图片
    public function imagePendant($com)
    {
        $pendantOneData = DB::table($com->id . '_pendant')->where('id', '1')->first();
        $pendantTwoData = DB::table($com->id . '_pendant')->where('id', '2')->first();
        $pendantThreeData = DB::table($com->id . '_pendant')->where('id', '3')->first();
        
        // 如果3张图片的ID都不存在或都未开启则显示默认图片
        if ((!$pendantOneData && !$pendantTwoData && !$pendantThreeData) || ($pendantOneData->disabled == 
            'true' && $pendantTwoData->disabled == 'true' && $pendantThreeData->disabled == 'true')) {
            $pendantData['count'] = 0;
        } else {
            if ($pendantOneData && $pendantOneData->disabled == 'false' && $pendantOneData->enclosures_id > 0) {
                $pendantOneData->paths = DB::table('enclosures')->where('id', $pendantOneData->enclosures_id)->pluck('path');
                $pendantData[] = $pendantOneData;
            }
            if ($pendantTwoData && $pendantTwoData->disabled == 'false' && $pendantTwoData->enclosures_id > 0) {
                $pendantTwoData->paths = DB::table('enclosures')->where('id', $pendantTwoData->enclosures_id)->pluck('path');
                $pendantData[] = $pendantTwoData;
            }
            if ($pendantThreeData && $pendantThreeData->disabled == 'false' && $pendantThreeData->enclosures_id > 0) {
                $pendantThreeData->paths = DB::table('enclosures')->where('id', $pendantThreeData->enclosures_id)->pluck('path');
                $pendantData[] = $pendantThreeData;
            }

            // 图片过滤赋值
            if (count($pendantData) == 3) {
                $pendantData['count'] = 3;
                $pendantData['one'] = $pendantData['0'];
                $pendantData['two'] = $pendantData['1'];
                $pendantData['three'] = $pendantData['2'];
            } else if (count($pendantData) == 2) {
                $pendantData['count'] = 2;
                $pendantData['one'] = $pendantData['0'];
                $pendantData['two'] = $pendantData['1'];
            }  else if (count($pendantData) == 1) {
                $pendantData['count'] = 1;
                $pendantData['one'] = $pendantData['0'];
            } else {
                $pendantData['count'] = 0;
            }
        }

        return $pendantData;
    }
}