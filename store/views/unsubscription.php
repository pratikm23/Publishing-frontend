<?php
require_once '../../preload/Store/config.php';

$title = 'Welcome to Daily Magic';
$siteDescription = '';
$siteKeywords = '';
$siteAuthor = '';

$includeCustomCss = null;
$includeCustomJs = null;

if($userStatus != 'NEWUSER' and $userStatus != 'UNKNOWN' and $userStatus != 'UNSUBSCRIBED' ){

	$data['unq_msg_id'] = '';
	$data['AppId'] = ($config :: BGWAPPID);
	$data['user_id'] = $userId;
			echo S3STATUS;	
	$serviceUrl = S3STATUS;

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $serviceUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, count($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);				
	$content = curl_exec ($ch);  
	$getCurlInfo = curl_getinfo($ch);
	curl_close ($ch); // close curl handle

	$output = json_decode($content, true);
		
	if(!empty($output)){
		if($output['status'] != 'UNSUBSCRIBED'){
			$opr = $output['operator'];
			$cpevent = $output['price_point'];			
							
			if( in_array($opr, $config['AllowedOperators']) ){							
				$current_url = $billingHost.$config['BGW']['OperatorConfig'][$opr]['BillingServiceUnSub'];
				$UnSubData['REQUESTTYPE'] = 'UNSUB';
				$UnSubData['CPEVENT'] = $cpevent;
				$UnSubData['MSISDN'] = $msisdn;
				$UnSubData['OPERATOR'] = $opr;
				$UnSubData['CMODE'] = $config['BGW']['OperatorConfig'][$opr]['Cmode'];
				
				$UnSubData['UID'] = $config['BGW']['Uid'];
				$UnSubData['PASS'] = $config['BGW']['Passwd'];
				
				$UnSubData['APPCONTID'] = 123;
				$UnSubData['TRANSID'] = $TransId;
				$UnSubData['UNITTYPE'] = 'UNSUBSCRIPTION';
				$UnSubData['RETURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/success.php';
				$UnSubData['FLRETURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/error.php';
				$UnSubData['OTHER1'] = '';
				$UnSubData['OTHER2'] = '';
				
				$pString = '';
				
				foreach($UnSubData as $key => $value){
					$pString .= $key.'='.$value.'&';
				}
				
				$pString = rtrim($pString, '&');
								
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $current_url);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $pString );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);  // RETURN THE CONTENTS OF THE CALL
				
				$info = curl_getinfo($ch);
				$output = curl_exec ($ch);  
				curl_close ($ch); // close curl handle
																
				$myFile      = $RootPath."/logs/UnSubLog_".$msisdn.".log";
				$fh = fopen($myFile, 'a') or die("can't open file");
				$date = date('Y-m-d H:i:s');
				fwrite($fh, "\r\n");	
				fwrite($fh, 'DATE AND TIME:');			
				fwrite($fh, $date);	
				fwrite($fh, "\r\n");	
				fwrite($fh, 'Response:');
				fwrite($fh, $output);
				fwrite($fh, "\r\n");
				fwrite($fh, 'MSISDN:');
				fwrite($fh, $msisdn );
				fwrite($fh, "\r\n");
				fwrite($fh, 'OPERATOR:');
				fwrite($fh, $opr );
				fwrite($fh, "\r\n");
				fwrite($fh, 'Transactionid:');
				fwrite($fh, $TransId );
				fwrite($fh, "\r\n");
				fwrite($fh, 'PricePoint:');
				fwrite($fh, $cpevent );
				fwrite($fh, "\r\n");
				fwrite($fh, 'CMode:');
				fwrite($fh, $cmode);
				fwrite($fh, "\r\n");			
				fwrite($fh, "--------------------------------------------------------------------------");
				fclose($fh);
												
				$headers = get_headers_from_curl_response($output);
				
			}
			include 'header.php';
?>
<tr>
	<td>
		<p><center><?=$headers['resDesc']?></center></p>
	</td>
</tr>
<?php
			include 'footer.php';			
		}else{
			header("Location: index.php");
			exit();
		}
	}
}else{
	header("Location: index.php");
	exit();
}

function FetchInfoFromCurlResponse($attrib, $obj){
	if(preg_match('#'.$attrib.': (.*)#', $obj, $r))
		return trim($r[1]);
}

?>