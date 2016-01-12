 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<meta name="viewport" content=" initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,width=device-width" /> 
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Welcome to Daily Magic</title>
 </head>
 <body style="margin:0">
 	



<?php
	//include_once '../../preload/Store/config.php';
	include_once "../../site/lib/functions.php";
	include_once "../controller/store.controller.php";
	// require_once('../../preload/Store/lib/config.class.php');
	// require_once "../../preload/Store/controller/user.class.php";
	// require_once('../../preload/Store/lib/curl.class.php');
	// require_once('../../preload/Store/controller/logger.class.php');
	// require_once('../../preload/Store/controller/campaign.class.php');

	// use Store\User as User;
	// use Store\Device as Device;
	// use Store\Config as Config;
	// use Store\Curl as Curl;
	// use Store\Campaign as Campaign;
	// use Store\Logger as Logger;


	$storeObj = new Store();
	// $userObj = new User\User();
			
	//STORE CONFIGS : 
	$PAGENAME = $_GET['pg'];
	$STOREID = 1;
	$MAINPATH = $_SERVER['DOCUMENT_ROOT']."PortletPublish_php";
	// $DOWNLOADPATH =  $MAINPATH."site/download.php";
	$DOWNLOADPATH =  "../views/download_cloud.php";
	
	$storeObj->setStoreConfigs($PAGENAME,$STOREID);

	
	$USERSTATUS = $storeObj->userStatus;
	
	
	// $USERSTATUS = "NEWUSER";
	// $USERSTATUS = "SUBSCRIBED";

	$PROMOID = $storeObj->promoId;
	$LINKURL = $storeObj->linkUrl;
	$SUBPARAM = $storeObj->subParam;
	
	// $SEARCHTXT = $_GET['search_txt'];
?>
	<div style="text-align:center">
				<img src="../../public/assets/img/d2clogo_320x45.png" />
	</div>
<?php
	$portletArray = $storeObj->getPortletContent();
	foreach($portletArray['portletData'] as $key=>$value){
     	require "portlets/portlet".$value->portletId.".php";
	}

	
?>
 </body>
 </html>