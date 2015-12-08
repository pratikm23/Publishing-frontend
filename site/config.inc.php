<?php
include 'lib/functions.php';
include 'lib/bootstrap.php';

$dbCentral = new Db( $config['Db']['central']['User'], $config['Db']['central']['Password'], $config['Db']['central']['Name']);
$dbCon = $dbCentral->getConnection();

$vendor_ids = array();

if(!empty($config['Vendor'])){
		
	$queryVendorId = "select * from vendor_detail where vd_name in (";	
	for($i=0;$i<count($config['Vendor']);$i++){
		if( $i < count($config['Vendor']) - 1){
			$queryVendorId .= "'".$config['Vendor'][$i]."', ";
		}else{
			$queryVendorId .= "'".$config['Vendor'][$i]."'";
		}
	}	
	$queryVendorId .= ") and vd_starts_on <= '".date("Y-m-d")."' and vd_end_on >= '".date("Y-m-d")."'";
		
	$resultVendor = $dbCentral->execute($dbCon, $queryVendorId);

	if( $dbCentral->getRecordsCount($resultVendor) > 0 ){		
		while($row = $resultVendor->fetch_assoc()){
			$vendor_ids[] = $row['vd_id'];
		}						
	}else{
		printData("No content available for defined Vendors");
		exit();
	}
}else{
	trigger_error("No Vendor present. Please define atleast one Vendor in <b>".LIB."bootstrap.php config parameter</b>", E_USER_ERROR);
	exit();
}

$vendor_id = implode(',',$vendor_ids);

$PageName = strtolower(ucfirst(pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME)));
$hostName = 'http://'.$_SERVER['HTTP_HOST'];									// http://mysite.com
$ServerUri = parse_url($_SERVER['REQUEST_URI']);								// [path] => / & [query] => abc=1
$urlPath = !empty($ServerUri['path']) ? $ServerUri['path'] : '';				// /
$RootPath = $_SERVER['DOCUMENT_ROOT'];											// /var/www/studiox/
$queryParameters = isset($ServerUri['query']) ? $ServerUri['query'] : null;		// abc=1
parse_str($queryParameters, $ExtractParamFromQueryParameters);					// [abc] => 1

//Database Configuration
$serviceHost = SVCHOST;
$billing_gateway = BILLING;
$billingHost = BILLINGHOST;

if($urlPath == ''){
	$linkUrl = $hostName.'/';
	$extractPath = explode('/',$hostName);
}else{
	$extractPath = explode('/',$urlPath);
	$linkUrl = $hostName;
	for($i=0;$i<count($extractPath) - 1;$i++){
		$linkUrl .= $extractPath[$i].'/';
	}
}

if(isset($_COOKIE[$config['CookieTag'].'_promo']) and !empty($_COOKIE[$config['CookieTag'].'_promo'])){
	$PromoId = $_COOKIE[$config['CookieTag'].'_promo'];
	$Tid = $_COOKIE[$config['CookieTag'].'_tid'];
	
	$checkForNonBannerId = explode('_',$PromoId);
	
	if($checkForNonBannerId[0] == 'z'){		
		if(isset($ExtractParamFromQueryParameters['promo']) and !empty($ExtractParamFromQueryParameters['promo'])){
			$PromoId = $ExtractParamFromQueryParameters['promo'];
			if( isset($ExtractParamFromQueryParameters['transaction_id']) ){
				$Tid = $ExtractParamFromQueryParameters['transaction_id'];
			}elseif( isset($ExtractParamFromQueryParameters['tid'])){
				$Tid = $ExtractParamFromQueryParameters['tid'];
			}elseif(isset($ExtractParamFromQueryParameters['af_tid'])){
				$Tid = $ExtractParamFromQueryParameters['af_tid'];
			}elseif( isset($ExtractParamFromQueryParameters['referrer']) ){
				$Tid = $ExtractParamFromQueryParameters['referrer'];		
				foreach($ExtractParamFromQueryParameters as $key => $value){
					if($key != 'c' and $key != 'promo' and $key != 'referrer'){
						$Tid .= '&'.$key.'='.$value;
					}
				}
				$Tid = rawurlencode($Tid);
			}elseif( isset($ExtractParamFromQueryParameters['click_id']) ){
				$Tid = $ExtractParamFromQueryParameters['click_id'];
			}elseif( isset($ExtractParamFromQueryParameters['vserv']) ){
				$Tid = $ExtractParamFromQueryParameters['vserv'];
			}elseif( isset($ExtractParamFromQueryParameters['track_no']) ){
				$Tid = $ExtractParamFromQueryParameters['track_no'];
			}elseif( isset($ExtractParamFromQueryParameters['adv_sub']) ){
				$Tid = $ExtractParamFromQueryParameters['adv_sub'];
			}elseif( isset($ExtractParamFromQueryParameters['subid']) ){
				$Tid = $ExtractParamFromQueryParameters['subid'];
			}elseif( isset($ExtractParamFromQueryParameters['sub_id']) ){
				$Tid = $ExtractParamFromQueryParameters['sub_id'];
			}elseif( isset($ExtractParamFromQueryParameters['kp']) ){
				$Tid = $ExtractParamFromQueryParameters['kp'];
			}elseif( isset($ExtractParamFromQueryParameters['clickID']) ){
				$Tid = $ExtractParamFromQueryParameters['clickID'];
			}elseif( isset($ExtractParamFromQueryParameters['rcid']) ){
				$Tid = $ExtractParamFromQueryParameters['rcid'];
			}elseif( isset($ExtractParamFromQueryParameters['uid']) ){
				$Tid = $ExtractParamFromQueryParameters['uid'];
			}elseif( isset($ExtractParamFromQueryParameters['aff_sub']) ){
				$Tid = $ExtractParamFromQueryParameters['aff_sub'];
			}elseif( isset($ExtractParamFromQueryParameters['clickid']) ){
				$Tid = $ExtractParamFromQueryParameters['clickid'];
			}elseif( isset($ExtractParamFromQueryParameters['click_ID']) ){
				$Tid = $ExtractParamFromQueryParameters['click_ID'];
			}elseif( isset($ExtractParamFromQueryParameters['kc']) ){
				$Tid = $ExtractParamFromQueryParameters['kc'];
			}else{
				$Tid = 0;
			}
		}else{
			$PromoId = 'z_'.uniqid();
			$Tid = 0;
		}
		setcookie($config['CookieTag'].'_promo', $PromoId, strtotime('today 23:59'), '/');
		setcookie($config['CookieTag'].'_tid', $Tid, strtotime('today 23:59'), '/');	
	}else{
		if(isset($ExtractParamFromQueryParameters['promo']) and !empty($ExtractParamFromQueryParameters['promo'])){
			$PromoId = $ExtractParamFromQueryParameters['promo'];
			if( isset($ExtractParamFromQueryParameters['transaction_id']) ){
				$Tid = $ExtractParamFromQueryParameters['transaction_id'];
			}elseif( isset($ExtractParamFromQueryParameters['tid']) ){
				$Tid = $ExtractParamFromQueryParameters['tid'];
			}elseif(isset($ExtractParamFromQueryParameters['af_tid'])){
				$Tid = $ExtractParamFromQueryParameters['af_tid'];
			}elseif( isset($ExtractParamFromQueryParameters['referrer']) ){
				$Tid = $ExtractParamFromQueryParameters['referrer'];		
				foreach($ExtractParamFromQueryParameters as $key => $value){
					if($key != 'c' and $key != 'promo' and $key != 'referrer'){
						$Tid .= '&'.$key.'='.$value;
					}
				}
				$Tid = rawurlencode($Tid);
						
			}elseif( isset($ExtractParamFromQueryParameters['click_id']) ){
				$Tid = $ExtractParamFromQueryParameters['click_id'];
			}elseif( isset($ExtractParamFromQueryParameters['vserv']) ){
				$Tid = $ExtractParamFromQueryParameters['vserv'];
			}elseif( isset($ExtractParamFromQueryParameters['track_no']) ){
				$Tid = $ExtractParamFromQueryParameters['track_no'];
			}elseif( isset($ExtractParamFromQueryParameters['adv_sub']) ){
				$Tid = $ExtractParamFromQueryParameters['adv_sub'];
			}elseif( isset($ExtractParamFromQueryParameters['subid']) ){
				$Tid = $ExtractParamFromQueryParameters['subid'];
			}elseif( isset($ExtractParamFromQueryParameters['sub_id']) ){
				$Tid = $ExtractParamFromQueryParameters['sub_id'];
			}elseif( isset($ExtractParamFromQueryParameters['kp']) ){
				$Tid = $ExtractParamFromQueryParameters['kp'];
			}elseif( isset($ExtractParamFromQueryParameters['clickID']) ){
				$Tid = $ExtractParamFromQueryParameters['clickID'];
			}elseif( isset($ExtractParamFromQueryParameters['rcid']) ){
				$Tid = $ExtractParamFromQueryParameters['rcid'];
			}elseif( isset($ExtractParamFromQueryParameters['uid']) ){
				$Tid = $ExtractParamFromQueryParameters['uid'];
			}elseif( isset($ExtractParamFromQueryParameters['aff_sub']) ){
				$Tid = $ExtractParamFromQueryParameters['aff_sub'];
			}elseif( isset($ExtractParamFromQueryParameters['clickid']) ){
				$Tid = $ExtractParamFromQueryParameters['clickid'];
			}elseif( isset($ExtractParamFromQueryParameters['click_ID']) ){
				$Tid = $ExtractParamFromQueryParameters['click_ID'];
			}elseif( isset($ExtractParamFromQueryParameters['kc']) ){
				$Tid = $ExtractParamFromQueryParameters['kc'];
			}else{
				$Tid = 0;
			}
		}else{
			$PromoId = $checkForNonBannerId[0];
		}
		setcookie($config['CookieTag'].'_promo', $PromoId, strtotime('today 23:59'), '/');
		setcookie($config['CookieTag'].'_tid', $Tid, strtotime('today 23:59'), '/');	
	}	
}else{
	if(isset($ExtractParamFromQueryParameters['promo']) and !empty($ExtractParamFromQueryParameters['promo'])){
		$PromoId = $ExtractParamFromQueryParameters['promo'];
		if( isset($ExtractParamFromQueryParameters['transaction_id']) ){
			$Tid = $ExtractParamFromQueryParameters['transaction_id'];
		}elseif( isset($ExtractParamFromQueryParameters['tid'])){
			$Tid = $ExtractParamFromQueryParameters['tid'];
		}elseif(isset($ExtractParamFromQueryParameters['af_tid'])){
			$Tid = $ExtractParamFromQueryParameters['af_tid'];
		}elseif( isset($ExtractParamFromQueryParameters['referrer']) ){
			$Tid = $ExtractParamFromQueryParameters['referrer'];		
			foreach($ExtractParamFromQueryParameters as $key => $value){
				if($key != 'c' and $key != 'promo' and $key != 'referrer'){
					$Tid .= '&'.$key.'='.$value;
				}
			}
			$Tid = rawurlencode($Tid);			
		}elseif( isset($ExtractParamFromQueryParameters['click_id']) ){
			$Tid = $ExtractParamFromQueryParameters['click_id'];
		}elseif( isset($ExtractParamFromQueryParameters['vserv']) ){
			$Tid = $ExtractParamFromQueryParameters['vserv'];
		}elseif( isset($ExtractParamFromQueryParameters['track_no']) ){
			$Tid = $ExtractParamFromQueryParameters['track_no'];
		}elseif( isset($ExtractParamFromQueryParameters['adv_sub']) ){
			$Tid = $ExtractParamFromQueryParameters['adv_sub'];
		}elseif( isset($ExtractParamFromQueryParameters['subid']) ){
			$Tid = $ExtractParamFromQueryParameters['subid'];
		}elseif( isset($ExtractParamFromQueryParameters['sub_id']) ){
			$Tid = $ExtractParamFromQueryParameters['sub_id'];
		}elseif( isset($ExtractParamFromQueryParameters['kp']) ){
			$Tid = $ExtractParamFromQueryParameters['kp'];
		}elseif( isset($ExtractParamFromQueryParameters['clickID']) ){
			$Tid = $ExtractParamFromQueryParameters['clickID'];
		}elseif( isset($ExtractParamFromQueryParameters['rcid']) ){
			$Tid = $ExtractParamFromQueryParameters['rcid'];
		}elseif( isset($ExtractParamFromQueryParameters['uid']) ){
			$Tid = $ExtractParamFromQueryParameters['uid'];
		}elseif( isset($ExtractParamFromQueryParameters['aff_sub']) ){
			$Tid = $ExtractParamFromQueryParameters['aff_sub'];
		}elseif( isset($ExtractParamFromQueryParameters['clickid']) ){
			$Tid = $ExtractParamFromQueryParameters['clickid'];
		}elseif( isset($ExtractParamFromQueryParameters['click_ID']) ){
			$Tid = $ExtractParamFromQueryParameters['click_ID'];
		}elseif( isset($ExtractParamFromQueryParameters['kc']) ){
			$Tid = $ExtractParamFromQueryParameters['kc'];
		}else{
			$Tid = 0;
		}
	}else{
		$PromoId = 'z_'.uniqid();
		$Tid = 0;
	}
	setcookie($config['CookieTag'].'_promo', $PromoId, strtotime('today 23:59'), '/');	
	setcookie($config['CookieTag'].'_tid', $Tid, strtotime('today 23:59'), '/');	
}

include 'plugin/wurfl/autoload.php';
include 'plugin/Mobile_Detect.php';
include 'Auth.php';

$PromoInterim = $config['PromoInterim'];

$D2C = new \Jet\App($PromoId, $Tid, $PromoInterim);
$D2C->Init();

$userStatus = $D2C->GetUserStatus();
$userId = $D2C->GetUserId();
$operator = $D2C->GetOperator();
$clientIp = $D2C->GetClientIp();
$msisdn = $D2C->GetMsisdn();
$OprSubParam = $D2C->GetOperatorSubscribeParam($operator);

$TransId = $D2C->GetTransId();
$Token = $D2C->GetToken();
$deviceInfo = $D2C->GetDeviceSize();
$mobileInfo = $D2C->GetMobileInfo();
$mobileDtd = $D2C->GetLanguage();
$sessionId = $D2C->GetSessionId();

if ($_SERVER['HTTP_NET_IP_ADDRESS'] == '101.222.243.244' || $_SERVER['HTTP_CLIENT_IP'] == '101.222.243.244') {
		$logFile = '/var/www/dailymagic/logs/bannertest-'.date('Y-m-d-H').'.log';
		file_put_contents($logFile, "\n Log : ".$userStatus." - ".$userId." - ".$operator." - ".$clientIp." - ".$msisdn." - ".$OprSubParam." - ".print_r($OprSubParam,true), FILE_APPEND);	
	}

$dbIkon = new DB($config['Db']['ikon']['User'], $config['Db']['ikon']['Password'], $config['Db']['ikon']['Name']);
$db = $dbIkon->getConnection();

$amount = $config['PPMapping'][$OprSubParam['CPEVENT']]['Amount'];
$duration = $config['PPMapping'][$OprSubParam['CPEVENT']]['Duration'];

$isTryBuyOffer = $D2C->isUser_Subscribed_for_Try_Buy_offer();
$isTryBuyOfferDetail = $D2C->GetTry_Buy_offerInfo();

$timestamp = strtotime(date('YmdHis'));

$CpUrl = $hostName.$urlPath;				// Current Page Url
$ErrorUrl = $linkUrl.'error.php';			// Fail Url

$param = 'REQUESTTYPE=NEW_SUB&CPEVENT='.$OprSubParam['CPEVENT'].'&APPCONTID=123&UNITTYPE=SUBSCRIPTION&MSISDN='.$msisdn.'&OPERATOR='.$operator.'&CMODE='.$OprSubParam['CMODE'].'&UID='.$config['BGW']['Uid'].'&PASS='.$config['BGW']['Passwd'].'&TRANSID='.$TransId.'&FLRETURL='.$ErrorUrl.'&OTHER1='.$OprSubParam['IMAGE'].'&OTHER2=&TOKENCALL='.$Token;
	
$deviceWidth = ($deviceInfo['Width'] <= 176) ? 176 : $deviceInfo['Width'] ;
$deviceHeight = $deviceInfo['Height'];

$imagemd5 = md5('image');
$videomd5 = md5('video');

$ThumbnailWidth = $config['Thumbnail']['Width'];
$ThumbnailHeight = $config['Thumbnail']['Height'];

$Logo = null;
$LogoVariants = array();
$previousFile = null;
$imagePath = $RootPath.'images/';
$LogoName = $config['SiteLogo'];

foreach(glob($imagePath.'*.*') as $file) {
	if( stripos(basename($file), $LogoName) !== false ){
		$LogoVariants[] = basename($file);
	}
}

for($i=0;$i<count($LogoVariants); $i++){
	$t1 = explode('-',$LogoVariants[$i]);
	$t1W = explode('x',$t1[1]);
	if( $deviceWidth != '0' ){
		if($t1W[0] > $deviceWidth){
			if($previousFile == null){
				$previousFile = $LogoVariants[$i];
			}
		}else{
			$Logo = $LogoName.'-176x24.png';
		}
	}
}
$Logo = $previousFile;

if( $Logo == null ){
	if( $mobileInfo['Resolution_Width'] < 240 ){
		$Logo = $LogoName.'-176x24.png';
	}elseif( $mobileInfo['Resolution_Width'] < 320 ){
		$Logo = $LogoName.'-240x34.png';
	}elseif( $mobileInfo['Resolution_Width'] < 360 ){
		$Logo = $LogoName.'-320x45.png';
	}elseif( $mobileInfo['Resolution_Width'] < 420 ){
		$Logo = $LogoName.'-360x51.png';
	}elseif( $mobileInfo['Resolution_Width'] < 480 ){
		$Logo = $LogoName.'-420x59.png';
	}else{		
		$Logo = $LogoName.'-480x68.png';
	}
}

//$userStatus = 'SUBSCRIBED';

$BannerConfig = array();

if (file_exists($RootPath.'lib/BannerConfig-'.$PageName.'.xml')) {
	$xml = simplexml_load_file($RootPath.'lib/BannerConfig-'.$PageName.'.xml');
	$BannerConfig = xml2array($xml);
}	

$showBanner = 'false';

if( !empty($BannerConfig) ){
	if($userStatus == 'UNSUBSCRIBED' or $userStatus == 'UNKNOWN' or $userStatus == 'NEWUSER'){
		$showBanner = $BannerConfig['Pre']['Show'];
		$imagePath = $RootPath.$BannerConfig['Pre']['BannerPath'];
		$topBannerUrl = $hostName.$BannerConfig['Pre']['Top']['Url'];
		$bottomBannerUrl = $hostName.$BannerConfig['Pre']['Bottom']['Url'];
		$BannerImageTypeTop = $BannerConfig['Pre']['Top']['ImageType'];
		$BannerImageTypeBottom = $BannerConfig['Pre']['Bottom']['ImageType'];
		$BannerFileNameTop = $BannerConfig['Pre']['Top']['Image'];
		$BannerFileNameBottom = $BannerConfig['Pre']['Bottom']['Image'];
		$BannerPath = $BannerConfig['Pre']['BannerPath'];
	}else{
		$showBanner = $BannerConfig['Post']['Show'];
		$imagePath = $RootPath.$BannerConfig['Post']['BannerPath'];
		$topBannerUrl = $BannerConfig['Post']['Top']['Url'];
		$bottomBannerUrl = $BannerConfig['Post']['Bottom']['Url'];
		$BannerImageTypeTop = $BannerConfig['Post']['Top']['ImageType'];
		$BannerImageTypeBottom = $BannerConfig['Post']['Bottom']['ImageType'];
		$BannerFileNameTop = $BannerConfig['Post']['Top']['Image'];
		$BannerFileNameBottom = $BannerConfig['Post']['Bottom']['Image'];
		$BannerPath = $BannerConfig['Post']['BannerPath'];
	}

	$previousFile = null;
	$topBanner = null;
	$bottomBanner = null;
	$topBannerVariant = $bottomBannerVariant = array();

	//foreach(glob($imagePath.'*.'.$BannerImageTypeTop) as $file) {
	//	if( stripos(basename($file), $BannerFileNameTop) !== false ){
	//		$topBannerVariant[] = basename($file);
	//	}
	//}
	//
	//foreach(glob($imagePath.'*.'.$BannerImageTypeBottom) as $file) {
	//	if( stripos(basename($file), $BannerFileNameBottom) !== false ){
	//		$bottomBannerVariant[] = basename($file);
	//	}
	//}
	//
	//for($i=0;$i<count($topBannerVariant); $i++){
	//	$t1 = explode('-',$topBannerVariant[$i]);
	//	$t1W = explode('x',$t1[1]);
	//	if( $deviceWidth != '0' ){
	//		if($t1W[0] > $deviceWidth){
	//			if($previousFile == null){
	//				$previousFile = $topBannerVariant[$i];
	//			}
	//			break;
	//		}else{
	//			$topBanner = 'new_top_banner-176x36.'.$BannerImageTypeTop;
	//		}
	//	}
	//}
	//$topBanner = $previousFile;
	//
	//$previousFile = null;
	//for($i=0;$i<count($bottomBannerVariant); $i++){
	//	$t1 = explode('-',$bottomBannerVariant[$i]);
	//	$t1W = explode('x',$t1[1]);
	//	if( $deviceWidth != '0' ){
	//		if($t1W[0] > $deviceWidth){
	//			if($previousFile == null){
	//				$previousFile = $bottomBannerVariant[$i];
	//			}
	//			break;
	//		}else{
	//			$bottomBanner = 'new_bottom_banner-176x36.'.$BannerImageTypeBottom;
	//		}
	//	}
	//}
	//$bottomBanner = $previousFile;

	if( $topBanner == null ){
		if( $mobileInfo['Resolution_Width'] < 240 ){
			$topBanner = $BannerFileNameTop.'-176x36.'.$BannerImageTypeTop;
		}elseif( $mobileInfo['Resolution_Width'] < 320 ){
			$topBanner = $BannerFileNameTop.'-240x45.'.$BannerImageTypeTop;
		}elseif( $mobileInfo['Resolution_Width'] < 360 ){
			$topBanner = $BannerFileNameTop.'-320x60.'.$BannerImageTypeTop;
		}elseif( $mobileInfo['Resolution_Width'] < 420 ){
			$topBanner = $BannerFileNameTop.'-360x68.'.$BannerImageTypeTop;
		}elseif( $mobileInfo['Resolution_Width'] < 480 ){
			$topBanner = $BannerFileNameTop.'-420x79.'.$BannerImageTypeTop;
		}elseif( $mobileInfo['Resolution_Width'] < 640 ){
			$topBanner = $BannerFileNameTop.'-480x90.'.$BannerImageTypeTop;
		}else{
			$topBanner = $BannerFileNameTop.'-640x120.'.$BannerImageTypeTop;
		}
	}
	
	
	if( $bottomBanner == null ){
		if( $mobileInfo['Resolution_Width'] < 240 ){
			$bottomBanner = $BannerFileNameBottom.'-176x36.'.$BannerImageTypeBottom;
		}elseif( $mobileInfo['Resolution_Width'] < 320 ){
			$bottomBanner = $BannerFileNameBottom.'-240x45.'.$BannerImageTypeBottom;
		}elseif( $mobileInfo['Resolution_Width'] < 360 ){
			$bottomBanner = $BannerFileNameBottom.'-320x60.'.$BannerImageTypeBottom;
		}elseif( $mobileInfo['Resolution_Width'] < 420 ){
			$bottomBanner = $BannerFileNameBottom.'-360x68.'.$BannerImageTypeBottom;
		}elseif( $mobileInfo['Resolution_Width'] < 480 ){
			$bottomBanner = $BannerFileNameBottom.'-420x79.'.$BannerImageTypeBottom;
		}elseif( $mobileInfo['Resolution_Width'] < 640 ){
			$bottomBanner = $BannerFileNameBottom.'-480x90.'.$BannerImageTypeBottom;
		}else{
			$bottomBanner = $BannerFileNameBottom.'-640x120.'.$BannerImageTypeBottom;
		}
	}
}
//if ($_SERVER['HTTP_NET_IP_ADDRESS'] == '101.60.163.51' || $_SERVER['NET-IP-ADDRESS'] == '101.60.163.51') {
//		$logFile = '/var/www/dailymagic/logs/bannertest-'.date('Y-m-d-H').'.log';
//		file_put_contents($logFile, "\n Log : ".$topBanner." - ".$bottomBanner." - ".$BannerFileNameTop." - ".$BannerFileNameBottom/*." - ".print_r($BannerConfig,true)*/, FILE_APPEND);			
//	}
if( !empty($ExtractParamFromQueryParameters) and isset($ExtractParamFromQueryParameters['c']) and $ExtractParamFromQueryParameters['c'] != '' and $ExtractParamFromQueryParameters['c'] != null and $ExtractParamFromQueryParameters['promo'] != '' and $ExtractParamFromQueryParameters['promo'] != null and isset($ExtractParamFromQueryParameters['promo']) ){
	if($showBanner == 'true'){
		$tmpLink = $linkUrl.$topBannerUrl;
		$topBannerUrl = $tmpLink.'?';
	}
	$SubParam = 'direct2Cg.php?';
	
	foreach($ExtractParamFromQueryParameters as $key => $value){
		if($key != 'transaction_id'){
			if($showBanner == 'true'){
				$topBannerUrl .= $key.'='.$value.'&';
			}
			$SubParam .= $key.'='.$value.'&';
		}
	}
	
	if(isset($_COOKIE[$config['CookieTag'].'_tid']) and $_COOKIE[$config['CookieTag'].'_tid'] != '' and $_COOKIE[$config['CookieTag'].'_tid'] != null and $_COOKIE[$config['CookieTag'].'_tid'] != '0'){
		$TransactionId = $_COOKIE[$config['CookieTag'].'_tid'];
	}else{
		$TransactionId = 0;
	}
	if($showBanner == 'true'){
		$topBannerUrl .= '&transaction_id='.rawurlencode($TransactionId).'&';
	}
	$SubParam .= '&transaction_id='.rawurlencode($TransactionId).'&';
}else{	
	if($showBanner == 'true'){
		$topBannerUrl .= '?c=1&promo='.$PromoId.'&';
	}
	$SubParam = 'direct2Cg.php?c=1&promo='.$PromoId.'&';
}

$SubText = null;
$DownloadLink = null;
if($queryParameters != null){	
	parse_str($queryParameters, $requestParam);	
	
	if(isset($requestParam['t']) and isset($requestParam['resDesc']) and isset($requestParam['status']) and $requestParam['t'] != '' and $requestParam['t'] != null){
		
		$QueryString = $requestParam['t'];
		
		$getResponseId = explode('?',$QueryString);
		
		$resDesc = $requestParam['resDesc'];
		$status = $requestParam['status'];

		$temp = explode('=',$getResponseId[1]);
		$responseId = $temp[1];
		
		$temp1 = explode('_',$getResponseId[0]);
	
		$DownloadLink = $hostName.'/download.php?t=';

		for($i=0;$i<count($temp1);$i++){
			if($i < count($temp1)-1){
				$DownloadLink .= $temp1[$i].'&';
			}else{
				$DownloadLink .= $temp1[$i];
			}
		}
								
		if( isset($responseId) and isset($resDesc) and isset($status) ){
			$SubText = $config['SubscribeText'];
		}
	}else{		
		if( isset($requestParam['responseId']) and isset($requestParam['resDesc']) and isset($requestParam['status']) and stripos($requestParam['status'], 'fail') === false ){
			$SubText = $config['SubscribeText'];
		}
	}
}


