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
 <body>
 	
 </body>
 </html>


<?php
	
	include "../controller/store.controller.php";

	$storeObj = new Store();
	//STORE CONFIGS : 
	$PAGENAME = $_GET['pg'];
	$STOREID = 1;
	$MAINPATH = $_SERVER['DOCUMENT_ROOT']."PortletPublish_php";
	// $DOWNLOADPATH =  $MAINPATH."site/download.php";
	$DOWNLOADPATH =  "../../site/download.php";
	
	$storeObj->setStoreConfigs($PAGENAME,$STOREID);

	
	$USERSTATUS = $storeObj->getUserStatus();
	$PROMOID = $storeObj->getPromoId();
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
