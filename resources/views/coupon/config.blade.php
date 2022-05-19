<form id="coupon_config_from" method="post">
    <div style="max-height: 380px;overflow: hidden;overflow-y: auto">
    <table class="table table-striped">
        <tbody>
        <tr>
            <th scope="row" class="text-right">是否开启计划任务</th>
            <td>
                <label>
                <input type="radio" value="0" @if(!$enabled) checked @endif name="enabled"> 否
                </label>
                <label>
                <input type="radio" value="1" @if($enabled) checked @endif name="enabled"> 是
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row" class="text-right">标题</th>
            <td>
                <input name="title" style="padding: 5px;width: 280px" value="{{isset($title)?$title:'发了一个拼手气红包'}}">
            </td>
        </tr>
        <tr>
            <th scope="row" class="text-right">祝福语</th>
            <td>
               <input name="content" style="padding: 5px;width: 280px"  value="{{isset($content)?$content:'恭喜发财,好运连连'}}">
            </td>
        </tr>

        @foreach($setting as $key=>$coupon)
            <tr>
                <th scope="row" class="text-right">{{$coupon['title']}}</th>
                <td>
                    <input type="hidden" value="{{$coupon['title']}}" name="setting[{{$key}}][title]"/>
                    总金额：<input style="width: 80px" type="number" name="setting[{{$key}}][amount]" value="{{$coupon['amount']}}">

                    总个数：<input style="width: 80px" type="number" name="setting[{{$key}}][num]" value="{{$coupon['num']}}">
                </td>

            </tr>
        @endforeach

        </tbody>
    </table>
    </div>
</form>