<?php

/**
 * Class ComAdminConfigController
 *
 * 公司后台系统设置控制器 by cooper
 */
class ComAdminConfigController extends ComAdminController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     *
     * @param $com
     *
     * @return mixed
     */
    public function index($com)
    {
        // 加载模板
        //
        return View::make('com/admin/config/index')->with('com', $com);
    }

    /**
     * 获取业务形态列表
     *
     * @param $com
     *
     * @return mixed
     */
    public function getTypes($com)
    {
        // 获取业务形态数据
        //
        $work_types = WorkType::orderBy('created_at', 'desc')->paginate();

        // 加载模板
        //
        return View::make('com/admin/config/types')->with('com', $com)->with(compact('work_types'));
    }

    /**
     * 创建业务形态
     *
     * @param $com
     *
     * @return mixed
     */
    public function getTypeCreate($com)
    {
        // 加载模板
        //
        return View::make('com/admin/config/type_create')->with('com', $com);
    }

    /**
     * 创建业务形态处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postTypeCreate($com)
    {
        // 验证规则
        //
        $rules = array(
            'work_type_name' => 'Required|min:2|unique:' . $com->id . '_work_type,work_type_name'
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 初始化业务类型模型
            //
            $work_type = new WorkType;

            // 组织业务类型数据
            //
            $work_type->work_type_name = e(Input::get('work_type_name'));
            $work_type->status         = TRUE;

            // 保存数据
            //
            if ($work_type->save())
            {
                // 成功后转跳
                //
                return Redirect::to($com->path . '/admin/config/types')->with('success', '创建 ' . $work_type->work_type_name . ' 成功 !');
            }
            else
            {
                // 失败转跳
                //
                return Redirect::to($com->path . '/admin/config/types')->with('error', '创建 ' . $work_type->work_type_name . ' 失败 !');
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 业务形态修改
     *
     * @param      $com
     * @param null $typeId
     *
     * @return mixed
     */
    public function getTypeEdit($com, $typeId = NULL)
    {
        // 获取业务形态数据
        //
        if (is_null($worktype = WorkType::find($typeId)))
        {
            return Redirect::to($com->path . '/admin/config/types')->with('error', '问题类型不存在');
        }

        // 加载模板
        //
        return View::make('com/admin/config/type_edit')->with('com', $com)->with(compact('worktype'));
    }

    /**
     * 业务形态修改处理
     *
     * @param      $com
     * @param null $typeId
     *
     * @return mixed
     */
    public function postTypeEdit($com, $typeId = NULL)
    {
        // 获取业务形态数据
        //
        if (is_null($worktype = WorkType::find($typeId)))
        {
            return Redirect::to($com->path . '/admin/config/types')->with('error', '业务形态不存在');
        }

        // 验证规则
        //
        $rules = array(
            'work_type_name' => 'Required|min:2|unique:' . $com->id . '_work_type,work_type_name,' . $typeId
        );

        // 验证信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证失败
        //
        if ($validator->fails())
        {
            // 返回错误信息
            //
            Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
        }

        // 更改数据
        //
        $worktype->work_type_name = e(Input::get('work_type_name'));

        if ($worktype->save())
        {
            // 返回成功
            //
            return Redirect::to($com->path . '/admin/config/types')->with('success', '更新成功');
        }

        // 返回错误信息
        //
        return Redirect::to($com->path . '/admin/config/type/' . $typeId)->with('error', '更新失败');

    }

    /**
     * 收发邮件配置
     *
     * @param $com
     *
     * @return mixed
     */
    public function getMail($com)
    {
        // 获取公司邮箱
        //
        $company_email = CompanyEmail::where('company_id', $com->id)->paginate();
        foreach ($company_email as $k => $v) {
            if ($v->validation_time == 0) {
                $company_email[$k]->validation_time = '-';
            } else {
                $company_email[$k]->validation_time = date('Y-m-d H:i:s', $v->validation_time);
            }
        }

        // 获取公司绑定组别
        foreach ($company_email as $k => $v) {
            $company_email[$k]->group_id = DB::table($com->id . '_groups')->where('email_id', $v->id)->pluck('id');
            $company_email[$k]->group_name = DB::table($com->id . '_groups')->where('email_id', $v->id)->pluck('group_name');
        }

        // 加载模板
        //
        return View::make('com/admin/config/mail')->with('com', $com)->with(compact('company_email'));
    }

    /**
     * 邮件创建
     *
     * @param $com
     *
     * @return mixed
     */
    public function getCreateMail($com)
    {
        // 加载模板
        //
        return View::make('com/admin/config/createmail')->with('com', $com);
    }

    /**
     * 收发邮件配置处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postCreateMail($com)
    {
        // 验证规则
        //
        $rules = array(
            'email' => 'Required|email|unique:company_email,email',
            'host' => 'Required:host',
            'port' => 'Required:port',
            'pass' => 'Required:pass',
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 初始公司邮件模型
            //
            $company_email             = new CompanyEmail();
            $company_email->company_id = $com->id;

            // 组织数据
            //disabled
            $company_email->email    = e(Input::get('email'));
            $company_email->group_id = 0;
            $company_email->disabled = 'true';
            $company_email->host = e(Input::get('host'));
            $company_email->port = e(Input::get('port'));
            $company_email->pass = e(Input::get('pass'));
            $company_email->validation_time = time();
            if (Input::get('inbox_path')) {
                $company_email->inbox_path = e(Input::get('inbox_path'));
            }

            if (Input::get('spam_path')) {
                $company_email->spam_path = e(Input::get('spam_path'));
            }

            // 保存数据
            //
            if ($company_email->save())
            {
                // 成功后转跳
                //
                return Redirect::to($com->path . '/admin/config/mail')->with('success', '保存成功 !');
            }
            else
            {
                // 失败转跳
                //
                return Redirect::to($com->path . '/admin/config/mail')->with('error', '保存失败 !');
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 邮箱配置查看
     *
     * @param $com
     *
     * @return mixed
     */
    public function getviewMail($com, $emailId = NULL)
    {
        // 获取公司邮箱
        //
        $company_email = CompanyEmail::where('company_id', $com->id)->where('id', $emailId)->first();

        // 加载模板
        //
        return View::make('com/admin/config/viewmail')->with('com', $com)->with(compact('company_email'));
    }

    /**
     * 邮箱配置处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postViewMail($com, $emailId = NULL)
    {
        // 判断启用邮箱验证是否已绑定组别
        if (Input::get('disabled') == 'false') {
            if (!DB::table($com->id . '_groups')->where('email_id', $emailId)->pluck('id')) {
                return Redirect::to($com->path . '/admin/config/mail')->with('error', '邮箱配置失败，此邮箱还未绑定组别，无法启用！');
            }
        }

        $mail_config = CompanyEmail::find($emailId);
        $mail_config->email = e(Input::get('email'));
        $mail_config->host = e(Input::get('host'));
        $mail_config->port = e(Input::get('port'));
        $mail_config->pass = e(Input::get('pass'));
        $mail_config->disabled = e(Input::get('disabled'));
        if (Input::get('inbox_path') == '') {
            $mail_config->inbox_path = 'INBOX';
        } else {
            $mail_config->inbox_path = e(Input::get('inbox_path'));
        }

        if (Input::get('spam_path') == '') {
            $mail_config->spam_path = 'SPAM';
        } else {
            $mail_config->spam_path = e(Input::get('spam_path'));
        }
        
        if ($mail_config->save()) {
            return Redirect::to($com->path . '/admin/config/mail')->with('success', '邮箱配置成功！');
        } else {
            return Redirect::to($com->path . '/admin/config/mail')->with('error', '邮箱配置失败！');
        }
    }

    // 组别绑定邮箱
    public function getGroup($com)
    {
        // 获取组别邮箱
        $company_email = CompanyEmail::where('company_id', $com->id)->paginate();
        foreach ($company_email as $k => $v) {
            $company_email[$k]->group_id = DB::table($com->id . '_groups')->where('email_id', $v->id)->pluck('id');
            $company_email[$k]->group_name = DB::table($com->id . '_groups')->where('email_id', $v->id)->pluck('group_name');
        }
        
        // 获取所有组别
        $group = Group::All();

        // 加载模板
        //
        return View::make('com/admin/config/group')->with('com', $com)->with(compact('group'))->with(compact('company_email'));
    }

    // 组别绑定邮箱处理
    public function postGroup($com)
    {
        // 默认清楚所有邮箱绑定信息
        DB::table($com->id . '_groups')->update(array('email_id' => 0));

        // 去除无效绑定项
        foreach ($_POST['group'] as $k => $v) {
            if ($v == 0) {
                unset($_POST['group'][$k]);
                unset($_POST['email'][$k]);
            }
        }

        if (count($_POST['group']) == 0 && count($_POST['email']) == 0) {
            return Redirect::to($com->path . '/admin/config/mail')->with('success', '邮箱绑定组别成功！');
        }

        while ($value = current($_POST['group'])) {
            $group[] = $value;
            next($_POST['group']);   
        }

        while ($value = current($_POST['email'])) {
            $email[] = $value;
            next($_POST['email']);   
        }

        // 判断是否存在重复组别
        if (count($group) != count(array_unique($group))) { 
            return Redirect::to($com->path . '/admin/config/mail')->with('error', '邮箱绑定组别失败，组别不能同时绑定多个邮箱！');
        } else {
            $validator = 'success';
            for ($i = 0; $i < count($email); $i++) {
                DB::table($com->id . '_groups')->where('id', $group[$i])->update(array('email_id' => $email[$i]));
            }
            
            return Redirect::to($com->path . '/admin/config/mail')->with('success', '邮箱绑定组别成功！');
        }
    }

    /**
     * 邮箱验证确认
     *
     * @param $com
     * @param $validationId
     *
     * @return mixed
     */
    public function getValidation($com, $validationId)
    {
        // 更新信息
        //
        if (DB::table('company_email_validation')->where('company_id', $com->id)->where('validation', 'false')->where('id', $validationId)->update(array('validation' => 'true')))
        {
            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/config/mail')->with('success', '通过邮箱转发认证确认成功！');
        }

        Return Redirect::back()->with('error', '通过邮箱转发认证确认失败 !');
    }


    // 公司首页挂件及logo配置
    public function getPendant($com)
    {
        // 获取公司及资源信息
        $comData  = DB::table('companys')->where('id', $com->id)->first();
        $logoPath = DB::table('enclosures')->where('id', $comData->logo)->first();
        if (!$logoPath)
        {
            $comData->enclosure_name = '';
            $comData->path           = '';
        }
        else
        {
            $comData->enclosure_name = $logoPath->enclosure_name;
            $comData->path           = $logoPath->path;
        }

        // 获取公司首页图片数据
        $pendantOneData   = DB::table($com->id . '_pendant')->where('id', '1')->first();
        $pendantTwoData   = DB::table($com->id . '_pendant')->where('id', '2')->first();
        $pendantThreeData = DB::table($com->id . '_pendant')->where('id', '3')->first();
        // 图片#1
        if (!$pendantOneData)
        {
            $pendantOneData = 0;
        }
        else
        {
            $pendantOneData->onepath = DB::table('enclosures')->
                where('id', $pendantOneData->enclosures_id)->first();
        }
        // 图片#2
        if (!$pendantTwoData)
        {
            $pendantTwoData = 0;
        }
        else
        {
            $pendantTwoData->twopath = DB::table('enclosures')->
                where('id', $pendantTwoData->enclosures_id)->first();
        }
        // 图片#3
        if (!$pendantThreeData)
        {
            $pendantThreeData = 0;
        }
        else
        {
            $pendantThreeData->threepath = DB::table('enclosures')->
                where('id', $pendantThreeData->enclosures_id)->first();
        }

        // 加载模板
        return View::make('com/admin/config/pendant')->with('com', $com)->with(compact('comData'))
            ->with(compact('pendantOneData'))->with(compact('pendantTwoData'))->with(compact('pendantThreeData'));
    }

    // 公司首页挂件及logo配置处理
    public function postPendant($com)
    {
        if (Input::get('clogo') == 'clogo')
        {
            // 公司logo
            //
            return $this->_cLogoPendant($com);
        }
        else if (Input::get('imageOne') == 'imageOne')
        {
            // 公司首页挂件#1处理
            //
            return $this->_imageOnePendant($com);
        }
        else if (Input::get('imageTwo') == 'imageTwo')
        {
            // 公司首页挂件#2处理
            //
            return $this->_imageTwoPendant($com);
        }
        else if (Input::get('imageThree') == 'imageThree')
        {
            // 公司首页挂件#3处理
            //
            return $this->_imageThreePendant($com);
        }
        else
        {
            Return Redirect::back();
        }
    }

    // 公司logo
    protected function _cLogoPendant($com)
    {
        // 验证规则
        $rules = array(
            'brief' => 'max:30',
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);
        if (!$validator->passes())
        {
            // 返回错误信息
            return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
        }

        // 验证是否上传图片
        if (Input::hasFile('file'))
        {
            $files = Input::file('file');
            $file  = $files[0];
            $str   = explode("/", $file->getMimeType());
            if ($str[0] != 'image')
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '请上传图片，格式为png、jpg、jpeg、gif或bmp！');
            }
            else if ($file->getSize() > (1024 * 1024))
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片过大，请控制在1M以内！');
            }

            $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
            $extension       = $file->getClientOriginalExtension();
            $filename        = str_random(8) . '.' . $extension;
            $upload_success  = $file->move($destinationPath, $filename);
            if ($upload_success)
            {
                $istr = explode(".", $filename);

                $encl                 = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName();
                $encl->type           = 'image';
                $encl->suffix         = $file->getClientOriginalExtension();
                $encl->path           = $destinationPath . '/' . $filename;
                if ($encl->save())
                {
                    $company        = Company::find($com->id);
                    $company->logo  = $encl->id;
                    $company->brief = Input::get('brief');

                    if ($company->save())
                    {
                        // 更新公司logo与简介成功
                        return Redirect::to($com->path . '/admin/config/pendant')->with('success', 'logo配置成功！');
                    }
                    else
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('error', 'logo配置失败！');
                    }
                }
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片失败！');
            }
        }
        else
        {
            $company        = Company::find($com->id);
            $company->brief = Input::get('brief');

            if ($company->save())
            {
                // 更新公司logo与简介成功
                return Redirect::to($com->path . '/admin/config/pendant')->with('success', 'logo配置成功！');
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', 'logo配置失败！');
            }
        }
    }

    // 公司首页图片#1处理
    protected function _imageOnePendant($com)
    {
        // 验证规则
        $rules = array(
            'brief' => 'max:80',
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);
        if (!$validator->passes())
        {
            // 返回错误信息
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #1 内容简介必须小于80个字符！');
        }

        $linkrules     = array(
            'link' => 'url',
        );
        $linkvalidator = Validator::make(Input::all(), $linkrules);
        if (!$linkvalidator->passes())
        {
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #1 站外链接不合法！(例：http://www.no)');
        }

        // 验证是否上传新图片
        if (Input::hasFile('file'))
        {
            $files = Input::file('file');
            $file  = $files[0]; // 获取上传数据
            $str   = explode("/", $file->getMimeType()); // 判断是否为图片
            if ($str[0] != 'image')
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '请上传图片，格式为png、jpg、jpeg、gif或bmp！');
            }
            else if ($file->getSize() > (1024 * 1024))
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片过大，请控制在1M以内！');
            }

            $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d'); // 指定上传路径
            $extension       = $file->getClientOriginalExtension(); // 获得数据资源
            $filename        = str_random(8) . '.' . $extension; // 组合不重复的资源名
            $upload_success  = $file->move($destinationPath, $filename); // 文件上传
            if ($upload_success)
            {
                // $istr = explode(".", $filename);

                $encl                 = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName(); // 获得原始文件名
                $encl->type           = 'image'; // 指定文件类型
                $encl->suffix         = $file->getClientOriginalExtension(); // 获得后缀名
                $encl->path           = $destinationPath . '/' . $filename; // 获取文件上传路径名
                if ($encl->save())
                {
                    if (Input::get('pendantID') == '1')
                    {
                        $pendant     = new Pendant;
                        $pendant->id = 1;
                    }
                    else
                    {
                        $pendantID = 1;
                        $pendant   = Pendant::find($pendantID);
                    }

                    $pendant->enclosures_id = $encl->id;
                    $pendant->brief         = Input::get('brief');
                    $pendant->link          = Input::get('link');
                    $pendant->disabled      = Input::get('disabled');

                    if ($pendant->save())
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#1配置成功！');
                    }
                    else
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#1配置失败！');
                    }
                }
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片失败！');
            }
        }
        else
        {
            if (Input::get('pendantID') == '1')
            {
                $pendant     = new Pendant;
                $pendant->id = 1;
            }
            else
            {
                $pendantID = 1;
                $pendant   = Pendant::find($pendantID);
            }

            $pendant->brief    = Input::get('brief');
            $pendant->link     = Input::get('link');
            $pendant->disabled = Input::get('disabled');

            if ($pendant->save())
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#1配置成功！');
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#1配置失败！');
            }
        }
    }

    // 公司首页图片#2处理
    protected function _imageTwoPendant($com)
    {
        // 验证规则
        $rules = array(
            'brief' => 'max:80',
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);
        if (!$validator->passes())
        {
            // 返回错误信息
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #2 内容简介必须小于80个字符！');
        }

        $linkrules     = array(
            'link' => 'url',
        );
        $linkvalidator = Validator::make(Input::all(), $linkrules);
        if (!$linkvalidator->passes())
        {
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #1 站外链接不合法！(例：http://www.no)');
        }

        // 验证是否上传新图片
        if (Input::hasFile('file'))
        {
            $files = Input::file('file');
            $file  = $files[0];
            $str   = explode("/", $file->getMimeType());
            if ($str[0] != 'image')
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '请上传图片，格式为png、jpg、jpeg、gif或bmp！');
            }
            else if ($file->getSize() > (1024 * 1024))
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片过大，请控制在1M以内！');
            }

            $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
            $extension       = $file->getClientOriginalExtension();
            $filename        = str_random(8) . '.' . $extension;
            $upload_success  = $file->move($destinationPath, $filename);
            if ($upload_success)
            {
                // $istr = explode(".", $filename);

                $encl                 = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName();
                $encl->type           = 'image';
                $encl->suffix         = $file->getClientOriginalExtension();
                $encl->path           = $destinationPath . '/' . $filename;
                if ($encl->save())
                {
                    if (Input::get('pendantID') == '2')
                    {
                        $pendant     = new Pendant;
                        $pendant->id = 2;
                    }
                    else
                    {
                        $pendantID = 2;
                        $pendant   = Pendant::find($pendantID);
                    }

                    $pendant->enclosures_id = $encl->id;
                    $pendant->brief         = Input::get('brief');
                    $pendant->link          = Input::get('link');
                    $pendant->disabled      = Input::get('disabled');

                    if ($pendant->save())
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#2配置成功！');
                    }
                    else
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#2配置失败！');
                    }
                }
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片失败！');
            }
        }
        else
        {
            if (Input::get('pendantID') == '2')
            {
                $pendant     = new Pendant;
                $pendant->id = 2;
            }
            else
            {
                $pendantID = 2;
                $pendant   = Pendant::find($pendantID);
            }

            $pendant->brief    = Input::get('brief');
            $pendant->link     = Input::get('link');
            $pendant->disabled = Input::get('disabled');

            if ($pendant->save())
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#2配置成功！');
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#2配置失败！');
            }
        }
    }

    // 公司首页图片#3处理
    protected function _imageThreePendant($com)
    {
        // 验证规则
        $rules = array(
            'brief' => 'max:80',
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);
        if (!$validator->passes())
        {
            // 返回错误信息
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #3 内容简介必须小于80个字符！');
        }

        $linkrules     = array(
            'link' => 'url',
        );
        $linkvalidator = Validator::make(Input::all(), $linkrules);
        if (!$linkvalidator->passes())
        {
            return Redirect::to($com->path . '/admin/config/pendant')->with('error', '图片设置 #1 站外链接不合法！(例：http://www.no)');
        }

        // 验证是否上传新图片
        if (Input::hasFile('file'))
        {
            $files = Input::file('file');
            $file  = $files[0];
            $str   = explode("/", $file->getMimeType());
            if ($str[0] != 'image')
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '请上传图片，格式为png、jpg、jpeg、gif或bmp！');
            }
            else if ($file->getSize() > (1024 * 1024))
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片过大，请控制在1M以内！');
            }

            $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
            $extension       = $file->getClientOriginalExtension();
            $filename        = str_random(8) . '.' . $extension;
            $upload_success  = $file->move($destinationPath, $filename);
            if ($upload_success)
            {
                // $istr = explode(".", $filename);

                $encl                 = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName();
                $encl->type           = 'image';
                $encl->suffix         = $file->getClientOriginalExtension();
                $encl->path           = $destinationPath . '/' . $filename;
                if ($encl->save())
                {
                    if (Input::get('pendantID') == '3')
                    {
                        $pendant     = new Pendant;
                        $pendant->id = 3;
                    }
                    else
                    {
                        $pendantID = 3;
                        $pendant   = Pendant::find($pendantID);
                    }

                    $pendant->enclosures_id = $encl->id;
                    $pendant->brief         = Input::get('brief');
                    $pendant->link          = Input::get('link');
                    $pendant->disabled      = Input::get('disabled');

                    if ($pendant->save())
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#3配置成功！');
                    }
                    else
                    {
                        return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#3配置失败！');
                    }
                }
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '上传图片失败！');
            }
        }
        else
        {
            if (Input::get('pendantID') == '3')
            {
                $pendant     = new Pendant;
                $pendant->id = 3;
            }
            else
            {
                $pendantID = 3;
                $pendant   = Pendant::find($pendantID);
            }

            $pendant->brief    = Input::get('brief');
            $pendant->link     = Input::get('link');
            $pendant->disabled = Input::get('disabled');

            if ($pendant->save())
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('success', '首页图片#3配置成功！');
            }
            else
            {
                return Redirect::to($com->path . '/admin/config/pendant')->with('error', '首页图片#3配置失败！');
            }
        }
    }

    // 工单级别列表
    public function getOrderLevel($com)
    {
        // 获取工单级别数据
        $order_type = OrderLevel::orderBy('created_at', 'desc')->paginate();

        // 加载模板
        return View::make('com/admin/config/order_level')->with('com', $com)->with(compact('order_type'));
    }

    // 工单级别创建
    public function getOrderLevelCreate($com)
    {
        // 加载模板
        return View::make('com/admin/config/order_level_create')->with('com', $com);
    }

    // 工单级别创建处理
    public function postOrderLevelCreate($com)
    {
        // 验证规则
        $rules = array(
            'name' => 'Required|min:2|max:6|unique:' . $com->id . '_order_level,name'
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        if ($validator->passes())
        {
            // 初始化工单级别模型
            $order_level = new OrderLevel;

            // 组织工单级别数据
            $order_level->name = e(Input::get('name'));
            
            // 保存工单级别数据
            if ($order_level->save()) {
                // 成功后转跳
                return Redirect::to($com->path . '/admin/config/orderLevel')->with('success', '创建 ' . $order_level->name . ' 成功!');
            } else {
                // 失败后转跳
                return Redirect::to($com->path . '/admin/config/orderLevel')->with('error', '创建 ' . $order_level->name . ' 失败!');
            }
        }

        // 返回错误信息
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    // 工单级别修改
    public function getOrderLevelEdit($com, $level_id = NULL)
    {
        // 获取工单级别数据
        if (is_null($order_level = OrderLevel::find($level_id))) {
            return Redirect::to($com->path . '/admin/config/orderLevel')->with('error', '工单级别不存在!');
        }

        // 加载模板
        return View::make('com/admin/config/order_level_edit')->with('com', $com)->with(compact('order_level'));
    }

    // 工单级别修改处理
    public function postOrderLevelEdit($com)
    {
        // 验证规则
        $rules = array(
            'name' => 'Required|min:2|max:6|unique:' . $com->id . '_order_level,name'
        );

        // 验证表单信息
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        if ($validator->passes())
        {
            // 初始化工单级别模型
            $order_level = OrderLevel::find(Input::get('level_id'));

            // 组织工单级别数据
            $order_level->name = e(Input::get('name'));
            
            // 保存工单级别数据
            if ($order_level->save()) {
                // 成功后转跳
                return Redirect::to($com->path . '/admin/config/orderLevel')->with('success', '编辑 ' . $order_level->name . ' 成功!');
            } else {
                // 失败后转跳
                return Redirect::to($com->path . '/admin/config/orderLevel')->with('error', '编辑 ' . $order_level->name . ' 失败!');
            }
        }

        // 返回错误信息
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    // 工单级别使用停用
    public function getOrderLevelUpdate($com, $level_id = NULL)
    {
        $order_level = OrderLevel::find($level_id);
        $order_level->disabled = Input::get('disabled');
        if ($order_level->save()) {
            return Redirect::to($com->path . '/admin/config/orderLevel')->with('success', '修改工单级别使用状态成功！');
        } else {
            return Redirect::to($com->path . '/admin/config/orderLevel')->with('success', '修改工单级别使用状态失败！');
        }
    }
}