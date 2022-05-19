<form id="menu_info_form" method="post">
    <input type="hidden" name="id" value="{{ $id }}">
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-right">中文名称</td>
            <td class="text-left"><input type="text" name="name" class="form-control" value="{{ $name }}" placeholder="中文名称"></td>
        </tr>
        <tr>
            <td class="text-right">英文标识</td>
            <td class="text-left"><input type="text" name="ident" class="form-control" value="{{ $ident }}" placeholder="小写字母、数字、下划线、中横线，最少3个字符"></td>
        </tr>
        <tr>
            <td class="text-right">类别</td>
            <td class="text-left">
                <select name="category" class="form-control">
                @foreach($category_array as $cate_key=>$cate_name)
                    <option value="{{ $cate_key }}"  @if($category == $cate_key) selected @endif>{{ $cate_name }}</option>
                @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td class="text-right">状态</td>
            <td class="text-left">
                <select name="status" class="form-control">
                    <option value="1" @if($status === 1) selected @endif>启用</option>
                    <option value="0" @if($status === 0) selected @endif>禁用</option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</form>