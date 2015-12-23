<?php
/*
$fh = fopen('/var/www/dailymagic/api/v1/apilog.log', 'a') or die("can't open file");
foreach( $_SERVER as $key => $value ){
	$tmp = $key.": ".$value."<br/>\n";
	fwrite($fh, $tmp);
}
fclose($fh);
print_r($_SERVER);
print_r(apache_request_headers());
*/

namespace JET;

class API{
	private $user;
	private $key;
	
	public function __construct(){
		$this->user = $_SERVER['PHP_AUTH_USER'];
		$this->key = $_SERVER['PHP_AUTH_PW'];
	}
	
	public function validate(){
		$request = array(
			'key' => $this->user,
			'secret' => $this->key
		);
		
		$result = $this->ExecuteCurl('http://192.168.1.159:82/api/v1/json', $request);
		print_r($result);
		/*
		$res = $client->request('GET', 'http://192.168.1.159:82/api/v1/json', [
			'auth' => [$this->user, $this->key]
		]);
		*/
	}
	
	private function ExecuteCurl($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, count($data));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	
		$output = curl_exec ($ch);		
		$info = curl_getinfo($ch);	
		$error = curl_error($ch);
		curl_close ($ch); // close curl handle	
		return array(
			'Response' => $output,
			'Info' => $info,
			'Error' => $error
		);
	}
}

$api = new \JET\API();
$api->validate();

?>