<form id="form_auto_keyword" method="POST">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>关键字</th>
            <th>回复语句</th>
            <th>删除</th>
        </tr>
        </thead>
        <tbody>
        @foreach($keywords as $keyword)
        <tr>
            <td><input class="form-control input_width" type="text" name="keyword[]" value="{{$keyword['keyword']}}"></td>
            <td><input class="form-control input_width" type="text" name="word[]" value="{{$keyword['msg']}}"></td>
            <td><a href="javascript:void(0)" onclick="deleteRecord(this)">删除</a></td>
        </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: right">
        <a href="javascript:void(0)" onclick="addKeyword()" class="btn-xs"><i class="fa fa-plus-circle"></i> 添加关键字 </a></td>
    </div>
</form>
<script>
   function deleteRecord(obj) {
       $(obj).parent().parent().remove();
   }
   function addKeyword() {
      $("#form_auto_keyword tbody").append('<tr>\n' +
          '            <td><input class="form-control input_width" type="text" name="keyword[]" value=""></td>\n' +
          '            <td><input class="form-control input_width" type="text" name="word[]" value=""></td>\n' +
          '            <td><a href="javascript:void(0)" onclick="deleteRecord(this)">删除</a></td>\n' +
          '        </tr>');
   }
</script>