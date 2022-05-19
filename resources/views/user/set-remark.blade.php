<form id="remark-form" method="post">
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-left">
                <textarea name="remark" cols="80" rows="5" id="remark-content" maxlength="64">{!! $value !!}</textarea>
                <div>还可以输入 <span id="remark-length" class="text-danger">64</span> 个字</div>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<script>
    $(document).ready(function () {
        $("#remark-content").bind('keyup change', function () {
            remarkLenth();
        });
        remarkLenth();
    });
    function remarkLenth() {
        var len = $("#remark-content").val().length;
        $("#remark-length").text(64 - len)
    }
</script>