<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
            <tr>
                <th colspan="2"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>邮箱</td>
                <td>{{ $user_info->user_name }}</td>
            </tr>
            <tr>
                <td>姓名</td>
                <td>{{ $user_info->name }}</td>
            </tr>
            <tr>
                <td>国家</td>
                <td>{{ $user_info->country }}</td>
            </tr>
            <tr>
                <td>电话</td>
                <td>{{ $user_info->mobile }}</td>
            </tr>
            <tr>
                <td>公司</td>
                <td>{{ $user_info->firm }}</td>
            </tr>
            <tr>
                <td>所属组</td>
                <td>{{ $user_info->group_name }}</td>
            </tr>
            <tr>
                <td>所属人</td>
                <td>{{ $user_info->admin_name }}</td>
            </tr>
            <tr>
                <td>创建时间</td>
                <td>{{ $user_info->created_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div><!-- /.row -->