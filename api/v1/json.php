<?php

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
	case 'POST':
		$request = $_POST;
		$result = process($request);  
		echo json_encode($result);
		break;	
	case 'GET':
		//process($request);  
		break;
	case 'PUT':
		//do_something_with_put($request);  
		break;	
	case 'HEAD':
		//do_something_with_head($request);  
		break;
	case 'DELETE':
		//do_something_with_delete($request);  
		break;
	case 'OPTIONS':
		//do_something_with_options($request);    
		break;
	default:
		//handle_error($request);  
		break;
}

function process($data){
	$result = ValidateKey($data);
	return $result;
}

function getDbDetails(){
	return parse_ini_file("db.ini");
}

function ValidateKey($data){
	$dbCredentials = getDbDetails();	
	$db = mysqli_connect($dbCredentials['host'][1], $dbCredentials['user'], $dbCredentials['password'], $dbCredentials['dbname']);
	$query = sprintf("select * from vendor_api_key where vak_api_key = '%s' and vak_secret_key = '%s'",$data['key'], $data['secret']);
	$result = mysqli_query($db, $query);
	
	if( mysqli_num_rows($result) > 0 ){
		$response = ProcessDbData($result);
	}else{
		$response = array();
	}
	mysqli_free_result($result);
	mysqli_close($db);
	return $response;
}

function ProcessDbData($obj){
	$result = array();
	if(!empty($obj)){
		while($row = mysqli_fetch_assoc($obj)){
			$result[] = $row;
		}
		return $result;
	}
	return $result;
}

function Convert2Json($obj){
	if(is_array($obj)){
		return json_encode($obj);
	}
	return $obj;
}

?>