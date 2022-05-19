@if (count($errors) > 0)
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong> <i class="fa fa-times-circle fa-fw"></i> 出错了！</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}！</li>
            @endforeach
        </ul>
    </div>
@endif