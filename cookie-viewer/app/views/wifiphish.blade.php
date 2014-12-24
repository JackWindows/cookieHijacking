@extends('default')

@section('content')
<table class="table table-hover table-bordered table-condensed">
	<thead>
		<tr>
			<td>主机名</td>
			<td>MAC地址</td>
			<td>IP地址</td>
			<td>设备制造商</td>
			<td>状态</td>
		</tr>
	</thead>
	<tbody>
		@foreach($lease as $l)
		<tr class="{{ $l['online']? 'success':'' }}">
			<td>{{ $l['hostname'] }}</td>
			<td>{{ $l['mac'] }}</td>
			<td>{{ $l['ip'] }}</td>
			<td>{{ $l['manufacturer'] }}</td>
			<td>{{ $l['online']? '在线':'离线' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@stop
