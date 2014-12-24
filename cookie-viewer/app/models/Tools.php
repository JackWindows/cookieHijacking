<?php

Class Tools{


static public function jsCookie($jsonCookie){
	$cookie=json_decode($jsonCookie,true);
	$result='javascript:';
	if(is_array($cookie))
		foreach($cookie as $key=>$value){
			$value=str_replace('"','\"',$value);
			$result.='void(document.cookie="'.$key.'='.$value.'");';
			}
	return $result;
}

static public function jsonCookie($jsonCookie,$domain){
	$cookie=json_decode($jsonCookie,true);
	$json=array();
	if(is_array($cookie))
		foreach($cookie as $key=>$value){
			$json[]=array(
				'domain'	=>	'.'.$domain,
				'name'		=>	strval($key),
				'value'		=>	$value,
				'path'		=>	'/'
				);
		}
	return json_encode($json,JSON_PRETTY_PRINT);
}

static public function findOUI($mac){
	$oui=json_decode(file_get_contents(storage_path().'/oui.json'),true);
	$key=strtoupper(substr($mac,0,8));
	if(isset($oui[$key]))
		return $oui[$key];
	else
		return 'unknown';
}

}
