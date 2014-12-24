@extends('default')

@section('content')
<table class="table table-hover table-bordered table-condensed">
	<thead>
		<tr>
			<td>用户名</td>
			<td>密码</td>
			<td>主机名</td>
			<td>MAC地址</td>
			<td>IP地址</td>
			<td>设备制造商</td>
			<td>上线时间</td>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $l)
		<tr>
			<td>{{ $l['name'] }}</td>
			<td>{{ $l['password'] }}</td>
			<td>{{ $l['hostname'] }}</td>
			<td>{{ $l['mac'] }}</td>
			<td>{{ $l['ip'] }}</td>
			<td>{{ $l['manufacturer'] }}</td>
			<td>{{ $l['time'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<a type="button" class="btn btn-primary" href="../../modules/captive/includes/module_action.php?service=captive&action=start&page=status" target="_blank">Start</a>
<a type="button" class="btn btn-primary" href="../../modules/captive/includes/module_action.php?service=captive&action=stop&page=status" target="_blank">Stop</a>
@stop
