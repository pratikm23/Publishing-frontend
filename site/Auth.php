<?php
namespace Jet;
use \Config;
use \Db;

date_default_timezone_set('Asia/Calcutta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}else{
	if(session_id() == '') {
		session_start();
	}
}

class App{
	private $hostName;
	private $serviceHostInternal;
	private $dbHost;
	private $dbUser;
	private $dbPswd;
	private $msisdn;
	private $clientIp;
	private $UserAgent;
	private $sessionId;
	private $detectDevice;
	private $wurflConfig;
	private $isAllowed = 'false';
	private $url;
	private $response;
	private $userId;
	private $userStatus;
	private $operator;
	private $authBy;
	private $requestFrom;
	private $transid;
	private $tokenId;
	private $AppId;
	private $BgwAppId;
	private $store;
	private $UID;
	private $referer;
	private $make;
	private $model;
	private $deviceId;
	private $deviceWidth;
	private $deviceHeight;
	private $defaultPrice_point;
	private $lang;
	private $mobileInfo = array();
	private $price_point = null;
	private $tryBuyOffer = 'false';
	private $tryBuyDays = 0;
	private $downloadPerDay_For_Try_Buy_offer = 0;
	private $download_For_Single_Day_Sub = 0;
	private $download_For_Full_Sub = 0;
	private $isFeature = 'true';
	private $FeaturePhoneUA = 'Micromax X2814/Q03C MAUI-Browser Profile/MIDP-2.0 Configuration/CLDC-1.1';
	private $PromoBannerId;
	private $TransactionId;
	private $CampaignName = null;
	private $CamapaignPartner = null;
	private $browser;
	private $imsi;
	private $currentPageName;
	private $config;
	private $currentUrl;
		
	public function __construct($promoId, $tid, $PromoInterim = array()) {
		$this->config = Config::getConfig();
		
		// Configure BGW params
		$this->BgwAppId = $this->config['BGW']['Id'];
		$this->AppId = $this->config['BGW']['AppId'];
		$this->store = $this->config['BGW']['Store'];
		$this->UID = $this->config['BGW']['Uid'];
		
		// Get HostName
		$this->hostName = "http://".$_SERVER['HTTP_HOST'];
		// Initialize Service and DB Host
		$this->serviceHostInternal = SVCHOST;
		$this->dbHost = DBHOST;
		
		// Initialize Db Users
		$this->dbUser = $this->config['Db']['ikon']['User'];
		$this->dbPswd = $this->config['Db']['ikon']['Password'];		
		$this->dbUserCampaign = $this->config['Db']['campaign']['User'];
		$this->dbPswdCampaign = $this->config['Db']['campaign']['Password'];
		
		// Get MSISDN, IMSI, Ip and User Agent
		$this->msisdn = $this->getMsidsn();			
		$this->imsi = $this->GetIMSI();			
		$this->clientIp = $this->getClientIpAddress();		
		$this->UserAgent = $_SERVER['HTTP_USER_AGENT'];
		
		// Initialize PromoId and TransactionId if any
		$this->PromoBannerId = $promoId;	
		$this->TransactionId = $tid;		
		
		// Promo Ids for Interim Page
		$this->PromoInterim = $PromoInterim;
		
		// Get Current page filename
		$this->currentPageName = strtolower(ucfirst(pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME)));
				
		// Create a Mobile Detection object 
		$this->detectDevice  = new \Detection\Mobile_Detect;
		
		if( SITE_MODE == 2 ){
			// Create a WURFL Configuration object 
			$this->wurflConfig = new \ScientiaMobile\WurflCloud\Config(); 
			// Set your WURFL Cloud API Key 
			$this->wurflConfig->api_key = '267006:lZPyhxN4jVoXt3fQMGkDuWAc1U8bRE06';  	
		}
	}
	
	public function Init(){
		// Get Browser details
		$browserDetails = get_browser(null, true);		
		$this->browser = $browserDetails['browser'];
		
		// Get MSISDN From Header
		// To create unique session id 
		// and store it in cookie
		$msisdn = $this->getMsidsn();
		
		$micro_date   = microtime();
		$date_array   = explode(" ", $micro_date);
		$milliseconds = substr($date_array[0], 2, 3);
		$date    = date('YmdHis');
				
		if( isset($_COOKIE['Unq_Sid']) and $_COOKIE['Unq_Sid'] != '' and $_COOKIE['Unq_Sid'] != null){
			$unq_sid = $_COOKIE['Unq_Sid'];	
			
			if($msisdn != 'UNKNOWN'){	
				if( substr($unq_sid, 0, 12) == '911111111111' ){
					$unq_sid = $msisdn.$date.$milliseconds;	
					setcookie('Unq_Sid', $unq_sid, strtotime('today 23:59'), '/');
				}elseif(substr($unq_sid, 0, 12) != $msisdn ){
					$unq_sid = $msisdn.$date.$milliseconds;	
					setcookie('Unq_Sid', $unq_sid, strtotime('today 23:59'), '/');
				}				
				$this->sessionId = $unq_sid;				
			}else{
				$this->sessionId = $unq_sid;
			}
		}else{
			if($msisdn == 'UNKNOWN'){
				$msisdn = '911111111111';
			}
			$unq_sid = $msisdn.$date.$milliseconds;	
			$this->sessionId = $unq_sid;
			setcookie('Unq_Sid', $unq_sid, strtotime('today 23:59'), '/');
		}
		
		$ServerUri = parse_url($_SERVER['REQUEST_URI']);
		$promoString = isset($ServerUri['query']) ? $ServerUri['query'] : null;
		parse_str($promoString, $promoParameters);
			
		if ( $this->detectDevice->isMobile() ) {
			$this->isAllowed = 'true';			
		}elseif ( $this->detectDevice->isTablet() ) {	
			$this->isAllowed = 'true';			
		}else{
			$this->isAllowed = 'true';	
		}

		if( $this->isAllowed == 'true' ){
					
			if( $this->msisdn == '' or $this->msisdn == 'UNKNOWN' ){
				//echo "MSISDN not found";
			}else{
				$this->setWakauCookie($this->config['CookieTag'].'_MSISDN',$this->msisdn);
			}
			
			$extractInfo = $this->GetMsisdnDetails($this->msisdn);
						
			// Set authService Response for log purpose
			$this->response = $extractInfo['Content'];
			//ERROR 3 :
			// $extractInfo['Response']['user_id'] = 1;
			// $extractInfo['Response']['user_status'] = 'NEWUSER';
			// $extractInfo['Response']['operator'] =  'voda';
			// $extractInfo['Response']['authby'] = 1;
			
			if($extractInfo['Response']['user_status'] == 'BLOCKED'){
				header("Location: ".$this->hostName.'/block.html');
				exit();
			}
			
			if($extractInfo['Response']['user_id'] != 0){				
				$this->userId = $extractInfo['Response']['user_id'];
				$this->userStatus = $extractInfo['Response']['user_status'];
				$this->operator = $extractInfo['Response']['operator']; 
										
				if( isset($extractInfo['Response']['authby']) ){
					if( $extractInfo['Response']['authby'] == 1 ){
						$this->authBy = "By DB";
					}elseif( $extractInfo['Response']['authby'] == 2 ){
						$this->authBy = "By IP";
					}elseif( $extractInfo['Response']['authby'] == 3 ){				
						$this->authBy = "By MSISDN";
					}elseif( $extractInfo['Response']['authby'] == 4 ){				
						$this->authBy = "By IMSI";
					}else{
						$this->authBy = "NO AUTH";
					}
				}else{
					$this->authBy = "NO AUTH";
				}
				
				
				$this->requestFrom = isset($extractInfo['Response']['servReqSource']) ? $extractInfo['Response']['servReqSource'] : 'WAP';
				
				if($this->userStatus != 'NEWUSER' and $this->userStatus != 'UNKNOWN' and $this->userStatus != 'UNSUBSCRIBED' ){ 
					$_SESSION['downloadAllowed'] = 'true';
				}else{
					$_SESSION['downloadAllowed'] = 'false';
				}
				
				if( $this->userStatus != 'NEWUSER' and $this->userStatus != 'UNSUBSCRIBED' and $this->userStatus != 'UNKNOWN'){
					// Logic to get current subscribed number price point	
					$SubPackData['AppId'] = $this->AppId;
					$SubPackData['user_id'] = $this->userId;
					
					$serviceUrl = $this->serviceHostInternal.'Service3.svc/substatus/';				
					$content = $this->ExecutePostCurl($serviceUrl, $SubPackData);
 					
					$outputSubPack = json_decode($content['Content'], true);	
 						
					$this->price_point = (string)$outputSubPack['price_point'];									
				}
				
			}else{						
				$this->userId = 'UNKNOWN';
				$this->userStatus = 'UNKNOWN';
				$this->operator = 'UNKNOWN';	
				$this->authBy = 'UNKNOWN';	
				$this->requestFrom = 'WAP';					
			}
			
			$this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none';
			
			$this->setWakauCookieObj($extractInfo['Response']);
			
			// Promo Code			
			$promoRedirect = false;
			$landingUrl = '';
						
			if(!empty($promoParameters) and isset($promoParameters['promo']) and $promoParameters['promo'] != '' and $promoParameters['promo'] != null and !isset($promoParameters['c']) and $this->currentPageName == 'index' and ctype_digit($promoParameters['promo']) ){
				
				$CampaignDetails = $this->GetFullCampaignDetails($this->PromoBannerId);
				
				if(!empty($CampaignDetails)){	
					$this->price_point = (string)$CampaignDetails['cp_promo_price_point'];
					
					$this->tokenId = $this->sessionId.'-'.$this->AppId.'-'.$this->PromoBannerId.'-'.$CampaignDetails['cp_banner_id'];
									
					if(intval($CampaignDetails['cp_cg_direct_flag']) == 1 ){							
						$mobileDetails = $this->GetMsisdnDetails($this->getMsidsn());	
											
						if(in_array( $this->PromoBannerId, $this->PromoInterim) ){
							$landingUrl = $this->hostName.'/proceed_to_subscription.php?c=1&f=home';	
						}else{
							$landingUrl = $this->hostName.'/direct2Cg.php?c=1&f=home';	
						}	
					}else{
						$landingUrl = 'http://'.$CampaignDetails['cp_landing_url'].'/?c=1';	
					}
					
					if( isset($promoParameters['referrer']) ){
						$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id=';
						$tpl = $promoParameters['referrer'];		
						foreach($promoParameters as $key => $value){
							if($key != 'c' and $key != 'promo' and $key != 'referrer'){
								$tpl .= '&'.$key.'='.$value;
							}
						}
						$tpl = rawurlencode($tpl);
						$landingUrl .= $tpl;
					}else{
						if( isset($promoParameters['af_tid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['af_tid'];
						}elseif( isset($promoParameters['tid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['tid'];
						}elseif( isset($promoParameters['click_id']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['click_id'];
						}elseif( isset($promoParameters['vserv']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['vserv'];
						}elseif( isset($promoParameters['track_no']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['track_no'];
						}elseif( isset($promoParameters['adv_sub']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['adv_sub'];
						}elseif( isset($promoParameters['subid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['subid'];
						}elseif( isset($promoParameters['sub_id']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['sub_id'];
						}elseif( isset($promoParameters['kp']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['kp'];
						}elseif( isset($promoParameters['clickID']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['clickID'];
						}elseif( isset($promoParameters['rcid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['rcid'];
						}elseif( isset($promoParameters['uid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['uid'];
						}elseif( isset($promoParameters['aff_sub']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['aff_sub'];
						}elseif( isset($promoParameters['clickid']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['clickid'];
						}elseif( isset($promoParameters['click_ID']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['click_ID'];
						}elseif( isset($promoParameters['kc']) ){
							$landingUrl .= '&promo='.$promoParameters['promo'].'&transaction_id='.$promoParameters['kc'];
						}else{								
							foreach($promoParameters as $key => $value){
								$landingUrl .= '&'.$key.'='.$value; 
							}	
						}	
					}
					
					$promoRedirect = true;
					
					//header("Location: ".$landingUrl);
					//exit();
				}else{
					$this->tokenId = $this->sessionId.'-'.$this->AppId.'-0-0';
				}
			}else{
				if( !empty($promoParameters) and isset($promoParameters['c']) and $promoParameters['c'] == '1' and ctype_digit($promoParameters['promo'])  ) {
					
					$CampaignDetails = $this->GetFullCampaignDetails($this->PromoBannerId);
					
					if(!empty($CampaignDetails)){				
						$this->tokenId = $this->sessionId.'-'.$this->AppId.'-'.$this->PromoBannerId.'-'.$CampaignDetails['cp_banner_id'];
						$this->price_point = (string)$CampaignDetails['cp_promo_price_point'];
					}else{
						$this->price_point = $this->config['BGW']['OperatorConfig'][$this->operator]['DefaultPP'];						
						$this->tokenId = $this->sessionId.'-'.$this->AppId.'-0-0';
					}
				}else{
					$promoParameters['c'] = 'z_ojwejrjwerlk_233e2';
					if( !empty($promoParameters) and !isset($promoParameters['c']) and ctype_digit($promoParameters['promo'])  ) {						
						$CampaignDetails = $this->GetFullCampaignDetails($this->PromoBannerId);
						$this->price_point = (string)$CampaignDetails['cp_promo_price_point'];
						$this->tokenId = $this->sessionId.'-'.$this->AppId.'-'.$this->PromoBannerId.'-'.$CampaignDetails['cp_banner_id'];
						
					}else{
						$this->price_point = $this->config['BGW']['OperatorConfig'][$this->operator]['DefaultPP'];
						$this->tokenId = $this->sessionId.'-'.$this->AppId.'-0-0';
					}
				}
			}
			
			$DeviceInfoResponse = (object)$this->GetWurlInfoFromDb();					
			$DeviceInfoResponse = json_decode($DeviceInfoResponse->Content, true);
			
			if( is_array($DeviceInfoResponse) and !empty($DeviceInfoResponse) ){		
				$DeviceInfoResponse = $DeviceInfoResponse[0];
				
				$this->lang = $DeviceInfoResponse['html_preferred_dtd'];												
				$this->make = $DeviceInfoResponse['device_brand'];
				$this->model = $DeviceInfoResponse['device_model'];						
				$this->deviceId = $DeviceInfoResponse['device_id'];
				$this->Streaming_Preferred_HTTP_Protocol = $DeviceInfoResponse['streaming_pref_http_protocal'];
				
				$this->deviceWidth = $DeviceInfoResponse['max_image_width'];
				$this->deviceHeight = $DeviceInfoResponse['max_image_height']; 
				
				$this->mobileInfo = array(
					'Resolution_Width' => $DeviceInfoResponse['device_res_width'],
					'Resolution_Height' => $DeviceInfoResponse['device_res_height'],
					'Image_Width' => $DeviceInfoResponse['max_image_width'],
					'Image_Height' => $DeviceInfoResponse['max_image_height'],
					'Wallpaper_Width' => $DeviceInfoResponse['wallpaper_preferred_width'],
					'Wallpaper_Height' => $DeviceInfoResponse['wallpaper_preferred_height']
				);
				
				$this->setWakauCookie($this->config['CookieTag'].'_screen_width', $DeviceInfoResponse['max_image_width']);
				
			}else{
				if( SITE_MODE == 2 ){
					$WurflInfo = $this->GetWurlInfoFromServer();
					
					$this->lang = "HTML";
					
					if( strtolower($WurflInfo['html_preferred_dtd']) == "html4" or strtolower($WurflInfo['html_preferred_dtd']) == "html5" ){
						$this->lang = "HTML";
					}else{
						$this->lang = "XHTML";
					}					
					$this->make = $WurflInfo['brand_name'];
					$this->model = $WurflInfo['model_name'];						
					$this->deviceId = $WurflInfo['id'];
					$this->Streaming_Preferred_HTTP_Protocol = $WurflInfo['streaming_preferred_protocol'];
					
					$this->deviceWidth = $WurflInfo['max_image_width'];
					$this->deviceHeight = $WurflInfo['max_image_height']; 
					
					$this->mobileInfo = array(
						'Resolution_Width' => $WurflInfo['resolution_width'],
						'Resolution_Height' => $WurflInfo['resolution_height'],
						'Image_Width' => $WurflInfo['max_image_width'],
						'Image_Height' => $WurflInfo['max_image_height'],
						'Wallpaper_Width' => $WurflInfo['wallpaper_preferred_width'],
						'Wallpaper_Height' => $WurflInfo['wallpaper_preferred_height']
					);
					
					$deviceInfo = array(
						'agent_id' => gmp_strval($this->gmphexdec(md5($this->UserAgent))),
						'user_agent' => rawurlencode($this->UserAgent),
						'device_id' => $WurflInfo['id'],
						'html_preferred_dtd' => $this->lang,
						'screen_width' => $WurflInfo['resolution_width'],
						'wallpaper_preferred_width' => $WurflInfo['wallpaper_preferred_width'],
						'wallpaper_preferred_height' => $WurflInfo['wallpaper_preferred_height'],
						'max_image_width' => $WurflInfo['max_image_width'],
						'max_image_height' => $WurflInfo['max_image_height'],
						'mp3' => empty($WurflInfo['mp3']) ? 0 : $WurflInfo['mp3'],
						'device_brand' => $WurflInfo['brand_name'],
						'device_model' => $WurflInfo['model_name'],
						'streaming_video' => ($WurflInfo['streaming_video'] == true) ? 1 : 0,
						'streaming_3gpp' => empty($WurflInfo['streaming_3gpp']) ? 0 : $WurflInfo['streaming_3gpp'],
						'streaming_mp4' => empty($WurflInfo['streaming_mp4']) ? 0 : $WurflInfo['streaming_mp4'],
						'streaming_flv' => empty($WurflInfo['streaming_flv']) ? 0 : $WurflInfo['streaming_flv'],
						'streaming_video_size_limit' => $WurflInfo['streaming_video_size_limit'],
						'pref_protocal' => $WurflInfo['streaming_preferred_protocol'],
						'streaming_pref_http_protocal' => $WurflInfo['streaming_preferred_http_protocol'],
						'wallpaper_gif' => empty($WurflInfo['wallpaper_gif']) ? 0 : $WurflInfo['wallpaper_gif'],
						'wallpaper_jpg' => empty($WurflInfo['wallpaper_jpg']) ? 0 : $WurflInfo['wallpaper_jpg'],
						'wallpaper_png' => empty($WurflInfo['wallpaper_png']) ? 0 : $WurflInfo['wallpaper_png'],
						'device_res_height' => $WurflInfo['resolution_height'],
						'device_res_width' => $WurflInfo['resolution_width'],
						'playback_3gpp' => empty($WurflInfo['playback_3gpp']) ? 0 : $WurflInfo['playback_3gpp'],
						'playback_mp4' => empty($WurflInfo['playback_mp4']) ? 0 : $WurflInfo['playback_mp4'],
						'browser' => $this->browser
					);
					
					$this->setWakauCookie($this->config['CookieTag'].'_screen_width', $WurflInfo['max_image_width']);
					
					$WurflUpdateResponse = $this->UpdateWurlfInfo($deviceInfo);	
				}
			}
					
			$LogFile = LOGS.'VisitorLog_'.date('Y-m-d-H').'.log';
			
			$DeviceWidthHeight = $this->deviceWidth.'x'.$this->deviceHeight;
			
			if( $this->msisdn != 'UNKNOWN' and $this->msisdn != '' and $this->msisdn != null and $this->operator != 'UNKNOWN' ){				
				if( !isset($_COOKIE[$this->config['CookieTag'].'_IMSI']) ){					
					$Imsi_url = $this->serviceHostInternal.'telcoService/imsiCir';
										
					$imsiData['IMSI'] = $this->imsi;
					$imsiData['MSISDN'] = $this->msisdn;					
					$imsiData['operator'] = $this->operator;
					$imsiData['make'] = $this->make;
					$imsiData['model'] = $this->model;
					$imsiData['browser'] = $this->browser;
					$imsiData['agent_id'] = gmp_strval($this->gmphexdec(md5($this->UserAgent)));
									
					$Imsicontent = $this->ExecutePostCurl($Imsi_url, $imsiData);				
										
					$t1 = LOGS.'IMSI_LOG'.date('Y-m-d').'.log';				
					$fh = fopen($t1, 'a') or die("can't open file");				
					fwrite($fh, "\n");				
					fwrite($fh, date('Y-m-d H:i:s').', ');
					fwrite($fh, $this->msisdn.', ');
					fwrite($fh, $this->imsi.', ');				
					fwrite($fh, $this->operator.', ');
					fwrite($fh, $this->make.', ');		
					fwrite($fh, $this->model.', ');		
					fwrite($fh, $this->browser.', ');
					fwrite($fh, gmp_strval($this->gmphexdec(md5($this->UserAgent))).', ');
					fwrite($fh, $Imsicontent['Content'].', ');  				
					fwrite($fh, "\n");
					fclose($fh);

					if( stripos($Imsicontent['Content'], 'IMSI-Circle record added') !== false or stripos($Imsicontent['Content'], 'IMSI-Circle updated') !== false ){
						setcookie($this->config['CookieTag'].'_IMSI', $this->imsi, strtotime('today 23:59'), '/');
					}
									
				}
			}
			
			$logDate = $this->udate('Y-m-d H:i:s.u');
						
			$this->Logging($LogFile, $logDate, $this->url, $this->msisdn, $this->clientIp, $this->operator, $this->authBy, $this->userStatus, $this->response, $this->make, $this->model, $_SERVER['REQUEST_URI'], $this->referer, $this->deviceId, $DeviceWidthHeight, $this->requestFrom, $this->store, $this->UID, $this->PromoBannerId);
			
			if($promoRedirect == true){
				header("Location: ".$landingUrl);
				exit();
			}			
			
		}else{
			header("Location: index.html");
			exit();
		}
	}
		
	public function getMsidsn(){        
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
	
	public function getClientIpAddress(){
		if (isset($_SERVER['HTTP_NET_IP_ADDRESS'])){
			return $_SERVER['HTTP_NET_IP_ADDRESS'];
		}elseif (isset($_SERVER['HTTP_X_CLIENT'])){
			return $_SERVER['HTTP_X_CLIENT'];
		}elseif (isset($_SERVER['NET_IP_ADDRESS'])){
			return $_SERVER['NET_IP_ADDRESS'];
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
            return $_SERVER['HTTP_CLIENT_IP'];
        }else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else if (isset($_SERVER['HTTP_X_FORWARDED'])){
            return $_SERVER['HTTP_X_FORWARDED'];
        }else if (isset($_SERVER['HTTP_FORWARDED_FOR'])){
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }else if (isset($_SERVER['HTTP_FORWARDED'])){
            return $_SERVER['HTTP_FORWARDED'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])){
			return $_SERVER['REMOTE_ADDR'];
		}else{
            return 'UNKNOWN';
        }        
    }
	
	private function GetMsisdnDetails($mobileNo){
		$extractInfo = array();
		//ERROR 2 : 
		// echo ($this->clientIp); 
		$this->clientIp = '10.35.2.5';
		$mobileNo = '919814883458';
		$this->url = $this->serviceHostInternal.'authService/?AppId='.$this->AppId.'&MSISDN='.$mobileNo.'&NET_IP_ADDRESS='.$this->clientIp.'&IMSI='.$this->imsi;
		
		
		$content = $this->ExecuteCurl($this->url);


		if( !empty(  $content['Content'] ) ) {
			$temp = explode(',', $content['Content']);
			for($i=0;$i<count($temp);$i++){
				$t2 = explode('=',$temp[$i]);
				$extractInfo[$t2[0]] = $t2[1]; 
			}
		}

		return array(
				'Response' => $extractInfo,
				'Content' => $content['Content']
		);
		
	}
	
	private function ExecuteCurl($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);								
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		$content = curl_exec ($ch);  
		$getCurlInfo = curl_getinfo($ch);	
		$curlError = curl_error($ch);
		curl_close ($ch); // close curl handle	
		
		return array(
			'Content' => $content,
			'Info' => $getCurlInfo,
			'Error' => $curlError
		);
	}
	
	private function ExecutePostCurl($url, $data){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);								
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);			
		curl_setopt($ch, CURLOPT_POST, count($data));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	
		$content = curl_exec ($ch);  
		$getCurlInfo = curl_getinfo($ch);	
		$curlError = curl_error($ch);
		curl_close ($ch); // close curl handle	
		
		return array(
			'Content' => $content,
			'Info' => $getCurlInfo,
			'Error' => $curlError
		);
	}
	
	private function GetWurlInfoFromServer(){				
		// Create the WURFL Cloud Client 
		$this->wurflClient = new \ScientiaMobile\WurflCloud\Client($this->wurflConfig, true); 
		// Detect your device 
		if($this->isFeature == 'true'){
			$uA['HTTP_USER_AGENT'] = $this->FeaturePhoneUA;
			$this->wurflClient->detectDevice($uA); 
		}else{
			$this->wurflClient->detectDevice($_SERVER); 
		}
							
		return $this->GetAllDeviceCapabilities();
	}
	
	private function GetWurlInfoFromDb(){		
		$deviceInfo = array(
			'agent_id' => gmp_strval($this->gmphexdec(md5($this->UserAgent)))
		);
		
		$serviceUrl = $this->serviceHostInternal.'telcoService/GetUserAgentInfo';
		
		return $this->ExecutePostCurl($serviceUrl, $deviceInfo);
		
	}
	
	private function UpdateWurlfInfo($obj){	
		$serviceUrl = $this->serviceHostInternal.'telcoService/AddUserAgentInfo';
		
		return $content = $this->ExecutePostCurl($serviceUrl, $obj);				
	}
	
	public function Redirect($url = null, $statusCode = 303){
		if($url){
			header("Location: ".$url, true, $statusCode);
			die();
		}else{
			header("Location: ".$this->hostName, true, 301);
			die();
		}
	}
	
	public function GetFullCampaignDetails($promoId){	
		if(ctype_digit($promoId)){			
			//Open a new connection to the MySQL server
			$dbCampaign = new Db($this->dbUserCampaign, $this->dbPswdCampaign, $this->config['Db']['campaign']['Name']);
			$dbCon = $dbCampaign->getConnection();
			
			$query = "SELECT * FROM cm_promo_detail as A, cm_promo_cg_details as B, cm_ad_client as C where A.cp_banner_id = B.cg_promo_id and A.cp_ad_client_id = C.ca_client_id and A.cp_promo_id = '".$this->PromoBannerId."'";		
			
			$resultCampaign = $dbCampaign->execute($dbCon, $query);
						
			if( $dbCampaign->getRecordsCount($resultCampaign) > 0 ){	
				return $dbCampaign->getData($resultCampaign);				
			}else{
				return array();
			}
		}else{
			return array();
		}
	}
	
	private function GetCampaignDetails($promoId){
		if( ctype_digit($promoId) ){
		
			$dbCampaign = new Db($this->dbUserCampaign, $this->dbPswdCampaign, $this->config['Db']['campaign']['Name']);
			$dbCon = $dbCampaign->getConnection();
			
			$queryCampaign = "SELECT * FROM cm_promo_detail as A, cm_promo_cg_details as B, cm_ad_client as C where A.cp_banner_id = B.cg_promo_id and A.cp_ad_client_id = C.ca_client_id and A.cp_app_id = 2 and A.cp_promo_id = '".$promoId."'";	
			
			$resultCampaign = $dbCampaign->execute($dbCon, $queryCampaign);
						
			if( $dbCampaign->getRecordsCount($resultCampaign) > 0 ){			
				$row = $dbCampaign->getData($resultCampaign);
				return array(
					'CGFlag' => $row['cp_cg_direct_flag'],
					'CampaignVendor' => $row['ca_client_name'],
					'CampaignName' => str_replace(' ', '_', $row['cp_promo_title'])
				);
			}else{
				return array(
					'CGFlag' => 0,
					'CampaignVendor' => $this->AppId,
					'CampaignName' => $this->AppId
				);
			}
		}else{
			return array(
				'CGFlag' => 0,
				'CampaignVendor' => $this->AppId,
				'CampaignName' => $this->AppId
			);
		}
	}
	
	public function GetUserAgent(){
		return $this->UserAgent;
	}
	
	public function GetClientIp(){
		return $this->clientIp;
	}
	
	public function GetAllDeviceCapabilities(){
		return $this->wurflClient->getAllCapabilities();
	}
	
	public function GetSpecificCapabilities($capability){
		return $this->wurflClient->getDeviceCapability($capability);
	}
	
	public function GetUserStatus(){
		return $this->userStatus;
	}
	
	public function GetOperator(){
		return $this->operator;
	}
	
	public function GetMsisdn(){
		return $this->msisdn;
	}
	
	public function GetUserId(){
		return $this->userId;
	}
	
	public function GetTransId(){		
		$micro_date   = microtime();
		$date_array   = explode(" ", $micro_date);
		$milliseconds = substr($date_array[0], 2, 3);
		$appid   = 123;
		$date    = date('YmdHis');
		if($this->msisdn == 'UNKNOWN' or $this->msisdn == ''){
			$this->transid = '1111111111' . $appid . $date . $milliseconds;
		}else{
			$msisdn_length = strlen((string)$this->msisdn);
		
			if($msisdn_length == 12) {
				$mnumber = substr($this->msisdn,2);
			}
			
			$this->transid = $mnumber . $appid . $date . $milliseconds;
			
		}
		return $this->transid;
	}
	
	public function GetToken(){			
		return $this->tokenId;
	}
	
	public function GetOperatorSubscribeParam($opr){
		
		if( in_array($opr, $this->config['AllowedOperators']) ){
			$cpevent = $this->price_point;
			$this->mobileInfo['Resolution_Width'] = 320;
			if($this->mobileInfo['Resolution_Width'] <= 240){
				$image_url = $this->hostName.'/cgImage/babes9/118965_176x264.jpg';
			}elseif($this->mobileInfo['Resolution_Width'] <= 320){
				$image_url = $this->hostName.'/cgImage/babes9/118965_240x360.jpg';
			}elseif($this->mobileInfo['Resolution_Width'] <= 480){
				$image_url = $this->hostName.'/cgImage/babes9/118965_320x480.jpg';
			}else{
				$image_url = $this->hostName.'/cgImage/babes9/118965_480x720.jpg';
			}
			
			return array(
				'CMODE' => $this->config['BGW']['OperatorConfig'][$opr]['Cmode'],
				'CPEVENT' => $cpevent,
				'IMAGE' => $image_url
			);
		}else{
			$cpevent = $this->price_point;
			return array(
				'CMODE' => '',
				'CPEVENT' => $cpevent,
				'IMAGE' => ''
			);
		}			
	}
	
	public function Logging($logFile, $date, $api, $msisdn, $clientIp, $operator, $authBy, $userStatus, $response, $make, $model, $currentPage, $referrerPage, $dId, $dWdH, $requestFrom, $Store, $uid, $promobannerId){	
		
		$fs = fopen($logFile, 'a') or die('Cannot open file');
		fwrite($fs, "\n");
		
        fwrite($fs, $date.',');
		
		fwrite($fs, trim($api).',');
        fwrite($fs, trim($msisdn).',');
        fwrite($fs, trim($clientIp).',');
        fwrite($fs, trim($operator).',');
        fwrite($fs, trim($authBy).',');		
        fwrite($fs, trim($userStatus).',');
        fwrite($fs, rawurlencode(trim($response)).',');
		
		fwrite($fs, trim($make).',');
        fwrite($fs, trim($model).',');
		
        fwrite($fs, trim(str_replace(',', '', $currentPage)).',');
        fwrite($fs, trim(str_replace(',', '', $referrerPage)).',');
		
        fwrite($fs, trim($dId).',');
        fwrite($fs, trim($dWdH).',');
        fwrite($fs, $requestFrom.',');
		$this->UserAgent = str_replace(',', ';', $this->UserAgent);  
        fwrite($fs, rawurlencode($this->UserAgent).',');
        fwrite($fs, $Store.',');
        fwrite($fs, $uid.',');  
        $SessionBannerId = $this->sessionId.','.$promobannerId.',';
		
        fwrite($fs, $SessionBannerId);  
		
		$campaignDetail = $this->GetCampaignDetails($promobannerId);
		$imsi = $this->GetIMSI();
		fwrite($fs, implode(',',$campaignDetail));  
		fwrite($fs, ','.$imsi.',');  
		
		$video_id = 0;
		fwrite($fs, $video_id.',');  
		fwrite($fs, rawurlencode($this->browser));  
		fwrite($fs, "\n");		
        fclose($fs);		
	}
	
	private function GetIMSI(){		
		if (isset($_SERVER['HTTP_X_NOKIA_IMSI'])){
            return $_SERVER['HTTP_X_NOKIA_IMSI'];
        }elseif (isset($_SERVER['HTTP-X-NOKIA-IMSI'])){
            return $_SERVER['HTTP-X-NOKIA-IMSI'];
        }elseif (isset($_SERVER['HTTP_X_IMSI'])){
            return $_SERVER['HTTP_X_IMSI'];
		}elseif (isset($_SERVER['HTTP-X-IMSI'])){
            return $_SERVER['HTTP-X-IMSI'];
        }elseif (isset($_SERVER['HTTP_IMSI'])){
            return $_SERVER['HTTP_IMSI'];
		}elseif (isset($_SERVER['HTTP-IMSI'])){
            return $_SERVER['HTTP-IMSI'];
        }else{
			return 0;
		}
	}
	
	public function setWakauCookieObj($cObj){	
		
		if( isset($_COOKIE[$this->config['CookieTag'].'_user_status']) and $_COOKIE[$this->config['CookieTag'].'_user_status'] != $cObj['user_status'] ){
			setcookie($this->config['CookieTag'].'_user_status', $cObj['user_status'], strtotime('today 23:59'), '/');
		}else{
			if( isset($_COOKIE[$this->config['CookieTag'].'_user_id']) and isset($_COOKIE[$this->config['CookieTag'].'_user_status']) and $_COOKIE[$this->config['CookieTag'].'_user_id'] != 0 and $_COOKIE[$this->config['CookieTag'].'_user_status'] != 'UNKNOWN' and $_COOKIE[$this->config['CookieTag'].'_user_status'] != 'UNSUBSCRIBED' and $_COOKIE[$this->config['CookieTag'].'_user_status'] != 'UNSUBPENDING' ){
				//setcookie('Wakau_user_status', $cObj['user_status'], strtotime('today 23:59'), '/');
			}else{
				foreach($cObj as $key => $value){
					if($key == 'user_id'){
						setcookie("D2C_".$key, $value, time() + (10 * 365 * 24 * 60 * 60), '/');
					}else{
						setcookie("D2C_".$key, $value, strtotime('today 23:59'), '/');
					}
				}			
			}
		}		
	}
	
	public function setWakauCookie($cookieName, $cookieValue){
		setcookie($cookieName, $cookieValue, strtotime('today 23:59'), '/');
	}
	
	public function updateWakauCookieObj($cObj){
		
	}
	
	public function getWakauCookie(){
		$cArray = array();
		if( isset($_COOKIE[$this->config['CookieTag'].'_user_id']) and isset($_COOKIE[$this->config['CookieTag'].'_user_status']) and isset($_COOKIE[$this->config['CookieTag'].'_MSISDN']) and isset($_COOKIE[$this->config['CookieTag'].'_operator']) ){
			$cArray['user_id'] = $_COOKIE[$this->config['CookieTag'].'_user_id'];
			$cArray['user_status'] = $_COOKIE[$this->config['CookieTag'].'_user_status'];
			$cArray['MSISDN'] = $_COOKIE[$this->config['CookieTag'].'_MSISDN'];
			$cArray['operator'] = $_COOKIE[$this->config['CookieTag'].'_operator'];
			return $cArray;
		}
		return $cArray;
	}
	
	private function isCookieSet(){
		if( isset($_COOKIE[$this->config['CookieTag'].'_user_id']) and isset($_COOKIE[$this->config['CookieTag'].'_user_status']) and isset($_COOKIE[$this->config['CookieTag'].'_MSISDN']) and isset($_COOKIE[$this->config['CookieTag'].'_operator']) ){
			return true;
		}
		return false;
	}
	
	private function printData($obj){
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}
	
	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	public function GetDeviceSize(){
		return array(
			'Width' => $this->deviceWidth,
			'Height' => $this->deviceHeight
		);
	}
	
	public function GetMobileInfo(){
		return $this->mobileInfo;
	}
	
	public function GetLanguage(){
		return $this->lang;
	}
	
	public function GetSessionId(){
		return $this->sessionId;
	}
	
	public function isUser_Subscribed_for_Try_Buy_offer(){
		return $this->tryBuyOffer;
	}
	
	public function GetPricePoint(){
		return $this->price_point;
	}

	public function GetTry_Buy_offerInfo(){
		return array(
			'IsTryBuyOfferOn' => $this->tryBuyOffer, 
			'TryBuyDays' => $this->tryBuyDays, 
			'Download_Per_Day_For_TryBuyOffer' => $this->downloadPerDay_For_Try_Buy_offer, 
			'Download_Single_Day_Sub' => $this->download_For_Single_Day_Sub, 
			'Download_Full_Sub' => $this->download_For_Full_Sub
		);
	}
	
	private function udate($format = 'u', $utimestamp = null) {
		if (is_null($utimestamp)){
			$utimestamp = microtime(true);
		}

		$timestamp = floor($utimestamp);
		$milliseconds = round(($utimestamp - $timestamp) * 1000000);

		return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
	}
	
	private function bchexdec($hex){
		$len = strlen($hex);
		for ($i = 1; $i <= $len; $i++)
			$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		
		return $dec;
	}
	
	private function gmphexdec($n) {
		$gmp = gmp_init(0);
		$mult = gmp_init(1);
		for ($i=strlen($n)-1;$i>=0;$i--,$mult=gmp_mul($mult, 16)) {
			$gmp = gmp_add($gmp, gmp_mul($mult, hexdec($n[$i])));
		}
		return $gmp;
	}
}

?>