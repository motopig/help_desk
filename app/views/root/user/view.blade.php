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
                <td>{{ $user->user_name }}</td>
            </tr>
            <tr>
                <td>姓名</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td>电话</td>
                <td>{{ $user->mobile }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div><!-- /.row -->