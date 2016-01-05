<?php
#userstatus -> NEWUSER || UNSUBSCRIBED
require_once '../../preload/Store/config.php';
// include_once('../preload/controller/user.class.php');
include_once '../controller/direct2CG.controller.php';
use Store\Direct2CG as Direct2CG;
use Store\Campaign as Campaign;
// use Store\User as User;
//get config parameters;
$t = $_GET['t'];
$n = $_GET['n'];
$d = $_GET['d'];
$m = $_GET['m'];
$i = isset($_GET['i']) ? $_GET['i'] : null;



$f = (isset($extractParams['f']))?$extractParams['f']:$currentPage;
$promo = (isset($extractParams['promo']))? $extractParams['promo']:$promo;
$price_point = (isset($extractParams['EventId']) and $extractParams['EventId'] != '' and $extractParams['EventId'] != null)? base64_decode($extractParams['EventId']): $OprSubParam['CPEVENT'];
if($userStatus == 'NEWUSER' or $userStatus == 'UNSUBSCRIBED' ){
	if( !in_array($operator, $config->allowedOperators) ){
		header("Location: error.php?responseId=999999&resDesc=Invalid Operator Info");
		exit();
	}else{

		$direct2cg = new Direct2CG\direct2cg($promo, $f);
		// $campaignDetails = new Campaign\Campaign();
		// $user->setCapaignDetails();

		$image_url = $direct2cg->getCGimages();

		$retUrl = $direct2cg->getUrlFromParams();

		

		// print_r($campaignDetails);

		// echo $fUrl;
		// print_r($hostName);

		if(stripos($hostName, "http://") !== false){
   			$fUrl = $hostName.'/error.php';   
 		 }else{
   			$fUrl = 'http://'.$hostName.'/error.php';   
  		 }

		// echo $price_point;
        if( isset($t) and isset($n) and isset($d) and isset($m) ){
			$n1 = base64_decode($n);
			if($i == null){
				$retUrl .= '?t='.$t.'_n='.$n1.'_d='.$d.'_m='.$m;
			}else{
				$retUrl .= '?t='.$t.'_n='.$n1.'_d='.$d.'_m='.$m.'_i='.$i;
			}
		}

		//if(!empty($extractParams) and isset($extractParams['promo']) and $extractParams['promo'] != '' and $extractParams['promo'] != null and ctype_digit($extractParams['promo'])){
		if(!(!empty($promo) and isset($promo) and $promo != '' and $promo != null and ctype_digit($promo))){
			$checkPromoId = explode("_",$promo);
			//echo "<pre>"; print_r($checkPromoId);
			if($checkPromoId[0] != 'z'){
				$fUrl = $campaignDetails->getNOKUrl();
				$retUrl = $campaignDetails->getLandingUrl();
				$price_point = $campaignDetails->getPromoPricePoint();
				$bannerId = $campaignDetails->getPromoBannerId();
				$direct2cg->logBGWBanner($msisdn,$operator, $TransId,$campaignDetails,$fUrl,$retUrl,$price_point,$bannerId);
			}
		}else{
			if( empty($OprSubParam) ){
				header("Location: ".$fUrl);
				exit();
			}else{
				$logCmode = $OprSubParam['CMODE'];
			}
		}

		$subscribeData = array(
			'transactionId' => $TransId,
			'msisdn' => $msisdn,
			'clientIp' => $clientIp,
			'retUrl' => $retUrl,
			'extractParams' => $extractParams,
			'promoBannerId' => $promo,
		);
		// print_r($subscribeData);
		
		$billing_gateway = 'http://103.43.2.5/'.$config->operatorData[$operator]['BillingServiceSub'].'?REQUESTTYPE=NEW_SUB&APPCONTID=123&UNITTYPE=SUBSCRIPTION&CPEVENT='.$price_point.'&MSISDN='.$msisdn.'&OPERATOR='.$operator.'&CMODE='.$OprSubParam['CMODE'].'&UID='.($config::UID).'&PASS='.($config::Paswd).'&TRANSID='.$TransId.'&RETURL='.$retUrl.'&FLRETURL='.$fUrl.'&OTHER1='.$image_url.'&OTHER2='.$hostName.'&TOKENCALL='.$Token;
		
		// $direct2cg->logSubscription($subscribeData);

		// fwrite($fs, 'Success Return url:');
		// fwrite($fs, $retUrl);
		// fwrite($fs, "\r\n");

		// fwrite($fs, 'Fail Return-. url:');
		// fwrite($fs, $fUrl);
		// fwrite($fs, "\r\n");

		// fwrite($fs, 'CPEVENT:');
		// fwrite($fs, $price_point);
		// fwrite($fs, "\r\n");

		// fwrite($fs, 'CMODE:');
		// fwrite($fs, $logCmode);
		// fwrite($fs, "\r\n");

		// fwrite($fs, 'CP IMAGE:');
		// fwrite($fs, $image_url);
		// fwrite($fs, "\r\n");

		// fwrite($fs, 'Token:');
		// fwrite($fs, $Token);
		// fwrite($fs, "\r\n");


		// fwrite($fs, 'Sub Url:');
		// fwrite($fs, $billing_gateway);
		// fwrite($fs, "\r\n");
		// fclose($fs);

		setcookie('D2C_promo', "", time()-3600, '/');
		setcookie('D2C_tid', "", time()-3600, '/');

		unset($_COOKIE['D2C_promo']);
		unset($_COOKIE['D2C_tid']);

		header("Location: ".$billing_gateway);
		exit();
	}
}else{
	$checkPromoId = explode("_",$extractParams['promo']);
	/*
	if($checkPromoId[0] != 'z'){
		header("Location: http://wakau.in/Wakau/celebritySubscribe/");
		exit();
	}else{*/
	if( $USERSTATUS == 'UNKNOWN' ){
		header("Location: error.php?responseId=999999&resDesc=Invalid Operator Info");
		exit();
	}else{
	#	header("Location: index.php");
		echo "User status is unknown.";
		exit();
	}
	//}
}


?>