@if (Session::has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>
            <i class="fa fa-check-circle fa-fw"></i> {{ Session::get('success') }}！
        </strong>
    </div>
@endif