<?php
//echo "hi";
/*NOTE EVENT ID SHOULD BE JET0006 eg  and currently it is coming from local db or hardcoded */
/* IF event id below is uncommented and headers are off the test page of apache is shown . */
/* If event id is given proper and headers are on the request goes to billing gateway.*/
require_once '../../preload/Store/config.php';
 $eventId= $_GET['EventId'];
// $eventId='Jet001';
// // print_r($Paswd);
// // print_r($config::UID);

if($userStatus == 'NEWUSER' or $userStatus == 'UNKNOWN' or $userStatus == 'UNSUBSCRIBED' ){
	// if( !in_array($operator, $config->allowedOperators) ){
	// 	 header("Location: index.php");
	// 	exit();
	// }else{
		if(isset($eventId) and $eventId != '' and $eventId != null ){
			
			$cpevent = base64_decode($eventId);
			
			$retUrl = $linkUrl.'index.php';				
			$ErrorUrl = $linkUrl.'error.php';
			
			$image_url = $hostName.'/cgImage/Footer_cg_image640x640.jpg';
			
			$billing_gateway = 'http://103.43.2.5/'.$config->operatorData[$operator]['BillingServiceSub'].'?REQUESTTYPE=NEW_SUB&APPCONTID=123&UNITTYPE=SUBSCRIPTION&CPEVENT='.$cpevent.'&MSISDN='.$msisdn.'&OPERATOR='.$operator.'&CMODE='.$OprSubParam['CMODE'].'&UID='.($config::UID).'&PASS='.($config::Paswd).'&TRANSID='.$TransId .'&RETURL='.$retUrl.'&FLRETURL='.$ErrorUrl.'&OTHER1='.$image_url.'&OTHER2='.$hostName.'&TOKENCALL='.$Token;
			// echo($billing_gateway);
			// exit;
			$subscribeData = array(
			'transactionId' => $TransId,
			'MSISDN' => $msisdn,
			'Client IP' => $clientIp,
			'Success Return url' => $retUrl,
			'CPEVENT' => $cpevent,
			'Operator' => $operator,
			'Fail Return url' => $ErrorUrl,
			'CMODE'=> $OprSubParam['CMODE'],
			'CP IMAGE' => $OprSubParam['IMAGE'],
			'Token' => $Token,
			'Sub Url' => $billing_gateway

		);
		    // $logger = new Logger($subscribeData);
			// $logger->logSubscribePack();
			
			header("Location: ".$billing_gateway);
			exit();
			
		}else{
			header("Location: ".$linkUrl);
			exit();
		}
	 // }
}else{
	// header("Location: ".$linkUrl);
	echo "User Already Subscribed";
	exit();
}
?>