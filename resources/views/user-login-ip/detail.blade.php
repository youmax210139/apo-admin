
<form id="transfer-to-parent-form" method="post">
    <table class="table table-hover table-striped">
        <thead>
			<tr>
				<th class="hidden-sm" data-sortable="false">用户ID</th>
				<th class="hidden-sm" data-sortable="false">用户名</th>
				<th class="hidden-sm" style="min-width: 120px" data-sortable="false">最后登录时间</th>
			</tr>
		</thead>
		<tbody>
		@foreach( $list as $val )
			<tr>
				<td>{{$val->id}}</td>
				<td>{{$val->username}}</td>
				<td>{{$val->last_time}}</td>
			</tr>
		@endforeach
		</tbody>
    </table>
</form>
