<div class="row">
    <div class="col-lg-12">

        <legend>基本信息</legend>

        <table class="table table-striped">
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
                <td>职位</td>
                <td>{{ $admin_user->position }}</td>
            </tr>
            <tr>
                <td>电话</td>
                <td>{{ $user_info->mobile }}</td>
            </tr>
            </tbody>
        </table>

        <legend>组信息</legend>

        <table class="table">
            <thead>
            <tr>
                <th>组名</th>
                <th>权限</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($group_info as $g)
            <tr>
                <td>
                    <span class="label label-primary">{{ $g->group_name }}</span>
                </td>
                <td>
                    @if ($g->grade == '1')
                    <span class="label label-success">组长</span>
                    @else
                    <span class="label label-info">组员</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div><!-- /.row -->