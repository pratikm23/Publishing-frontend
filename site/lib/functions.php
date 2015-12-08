<?php
function is_dir_empty($dir) {
	if (!is_readable($dir)) return null; 
	$handle = opendir($dir);
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			return false;
		}
	}
	return true;
}

function getMsidsn(){  
	if (isset($_SERVER['X-MSISDN'])){
		return $_SERVER['X-MSISDN'];
	}elseif (isset($_SERVER['X_MSISDN'])){
		return $_SERVER['X_MSISDN'];
	}elseif (isset($_SERVER['HTTP_X_MSISDN'])){
		return $_SERVER['HTTP_X_MSISDN'];
	}elseif (isset($_SERVER['X-UP-CALLING-LINE-ID'])){
		return $_SERVER['X-UP-CALLING-LINE-ID'];
	}elseif (isset($_SERVER['X_UP_CALLING_LINE_ID'])){
		return $_SERVER['X_UP_CALLING_LINE_ID'];
	}elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])){
		return $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
	}elseif (isset($_SERVER['X_WAP_NETWORK_CLIENT_MSISDN'])){
		return $_SERVER['X_WAP_NETWORK_CLIENT_MSISDN'];
	}elseif (isset($_SERVER['HTTP_MSISDN'])){
		return $_SERVER['HTTP_MSISDN'];
	}elseif (isset($_SERVER['HTTP-X-MSISDN'])){
		return $_SERVER['HTTP-X-MSISDN'];
	}elseif (isset($_SERVER['MSISDN'])){
		return $_SERVER['MSISDN'];
	}elseif (isset($_SERVER['HTTP_X_NOKIA_MSISDN'])){
		return $_SERVER['HTTP_X_NOKIA_MSISDN'];
	}else{
		return 'UNKNOWN';
	}
}

function printData($obj){
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}

function truncate($string, $length, $dots = "...") {
	echo strlen($string);
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function getValuefromTable($con, $table, $field, $val){
	$query = 'select * from '.$table.' where '.$field.' = '.$val;	
	$tmpResult = $con->query($query);
	
	if( $tmpResult->num_rows > 1 ){
		$tmpArray = array();
		while($row = $tmpResult->fetch_assoc()){
			$tmpArray[] = $row;
		}
		return $tmpArray;
	}else{
		return $tmpResult->fetch_assoc();
	}	
}

function get_headers_from_curl_response($response){
    $headers = array();

    foreach (explode("\r\n", $response) as $i => $line){
		if ($i === 0 or $i === 1 or $i === 2){
            //$headers['http_code'] = $line;
        }else{
            list ($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
	}
    return $headers;
}

function GetPricePointInfo($pricePoint){
	$PricePointInfo = array();
	if( $pricePoint == 'BANNER0001' ){
		$PricePointInfo = array(
			'Duration' => 'Daily',
			'Amount' => '3'
		);
	}elseif( $pricePoint == 'BANNER0002' ){
		$PricePointInfo = array(
			'Duration' => 'Monthly',
			'Amount' => '30'
		);
	}elseif( $pricePoint == 'JET0001' ){
		$PricePointInfo = array(
			'Duration' => '15 days',
			'Amount' => '45'
		);
	}elseif( $pricePoint == 'JET0002' ){
		$PricePointInfo = array(
			'Duration' => '10 days',
			'Amount' => '30'
		);
	}elseif( $pricePoint == 'JET0003' ){
		$PricePointInfo = array(
			'Duration' => 'Weekly',
			'Amount' => '21'
		);
	}elseif( $pricePoint == 'JET0004' ){
		$PricePointInfo = array(
			'Duration' => '5 days',
			'Amount' => '15'
		);
	}elseif( $pricePoint == 'JET0005' ){
		$PricePointInfo = array(
			'Duration' => 'Daily',
			'Amount' => '3'
		);
	}elseif( $pricePoint == 'BANNER0003' ){
		$PricePointInfo = array(
			'Duration' => '10 days',
			'Amount' => '30'
		);
	}
	
	return $PricePointInfo;
}

function getRandomFileFromDirectory($dir){
    $files = glob('../'.$dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}

function xml2array($xmlObject){
	
	$out = array();
	
	foreach ( (array) $xmlObject as $index => $node ){	
		if( is_object($node) and empty($node)){
			$out[$index] = '';
		}else{
			$out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
		}
	}
	
    return $out;
}



?>