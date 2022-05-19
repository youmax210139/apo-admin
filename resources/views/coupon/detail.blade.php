<div style="max-height: 500px;overflow: hidden;overflow-y: auto">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>金额</th>
            <th>领取人</th>
            <th>领取IP</th>
            <th>领取时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($coupons as $coupon)
            <tr>
                <th scope="row">{{$coupon->id}}</th>
                <td>{{$coupon->amount}}</td>
                <td>{{$coupon->collect_username?$coupon->collect_username:'未领取'}}</td>
                <td>{{$coupon->collect_ip?$coupon->collect_ip:'--'}}</td>
                <td>{{$coupon->collect_username?$coupon->updated_at:'--'}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>