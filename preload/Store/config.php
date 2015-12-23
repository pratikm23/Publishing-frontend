<?php
include_once('controller\user.class.php');
include_once('lib\config.class.php');
include_once('lib\curl.class.php');
include_once('controller\device.class.php');
include_once('controller\campaign.class.php');
include_once('controller\logger.class.php'); 
//include_once "controller/direct2CG.controller.php";

use Store\User as User;
use Store\Device as Device;
use Store\Config as Config;
use Store\Curl as Curl;
use Store\Campaign as Campaign;
use Store\Logger as Logger;

$user = new User\User();

$promo = $user->PromoBannerId;
//$userStatus = $user->getUserStatus();
$userStatus = "SUBSCRIBED";
// $userId = $user->getUserId();
$userId=2761661;


$operator = $user->getOperator();
$clientIp = $user->getClientIp();
$msisdn = $user->getMsisdn();
$OprSubParam = $user->getOperatorSubscribeParam($operator);

$TransId = $user->getTransId();
$Token = $user->getToken();
// $deviceInfo = $user->getDeviceSize();
// $mobileInfo = $user->getMobileInfo();

// $mobileDocTD = $user->getLanguage(); //doc type declaration xhtml/html5
$sessionId = $user->getSessionId();
$extractParams = $user->getQueryParams();
$config = $user->getConfigData();
//$campaignDetails = $user->getCampaignDetails();

$currentPage = $user->getCurrentPage();
$hostName = $user->hostName;
$linkUrl =$user->getLinkUrl();
$subParam = $user->getSubParam();

?>