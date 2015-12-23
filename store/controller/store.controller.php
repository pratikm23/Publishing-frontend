<?php
	use Store\Curl as Curl;
	class Store {
		public function __construct(){
			/* Bootstrap.php includes config and db.php  */
			// include "../../site/lib/bootstrap.php";
			// include_once "../../preload/Store/config.php";
			include_once '../../preload/Store/config.php';
			include_once "../models/store.model.php";	
			include_once "../../site/lib/functions.php";
			
			// $this->userStatus = $userStatus;
			$this->userStatus = "SUBSCRIBED";
			$this->promoId = $promo;
			$this->linkUrl = $linkUrl;
			$this->subParam = $subParam;

			$this->curlObj = new Curl\Curl();

			// $dbCMS = new Db($config['Db']['icon_cms']['User'], $config['Db']['icon_cms']['Password'],$config['Db']['icon_cms']['Name']);

			// $this->dbCon = $dbCMS->getConnection();
		}

		public function setStoreConfigs($pageName,$storeId){
			$this->pageName = $pageName;
			$this->storeId = $storeId;
		}
		
		public function getPortletContent(){
			
			$url = "http://localhost:9090/wICONapi/web/api/v1/index.php/pages/pageDetails";
			$data = array(
					"pageName" => $this->pageName,
					"storeId" => $this->storeId,
					"deviceHeight" => 200,
					"deviceWidth" => 200
				 );
			$data = json_encode($data);
			// print_r($data);
			$result_portletContent = $this->curlObj->ExecutePostCurl($url,$data);

			// print_r($result_portletContent);
			// print_r(json_decode($result_portletContent['Content'])->message->potletMapDetails);
			// exit;
			// print_r($result_portletContent);

			// $result_packageids = getPackageIdsByPageName($this->dbCon,$this->pageName,$this->storeId);
			// $packageIds = Array();
			$portlet = Array();
			// while( $res = $result_packageids->fetch_assoc()){
			$portlet['portletData'] = json_decode($result_portletContent['Content'])->message->potletMapDetails;
			$portlet['portletContent'] = json_decode($result_portletContent['Content'])->message->portletDetails;

			// print_r($portlet);
			// exit;

			// 	if($res['packageId'] > 0)
			// 		$packageIds[] = $res['packageId'];
			// }

			// $result_portletContent = getPortletContentByPackageId($this->dbCon,$packageIds);
		
			/*while( $res = $result_portletContent->fetch_assoc()){
					$portlet['portletContent'][] = $res;
			}*/
			// var_dump($portlet);
			// exit;
			return $portlet;
		}

		public function getPortletWallpapers($portletId){
			$arr = Array();
			$portletArray = $this->getPortletContent();
			foreach ($portletArray['portletContent'] as $key => $value) {

					if($value->cd_name == 'Wallpaper' && $portletId == $value->portletId){
						$arr[] = $value;
					}
				
			}
			return $arr;
		}

		public function getPortletWallpapersBySearchKeywords($portletId){
			$arr = Array();
			foreach ($this->getPortletContent()['portletContent'] as $key => $value) {
					if($value->cd_name == 'Wallpaper' && $portletId == $value->portletId){
						$arr[] = $value;
					}
				
			}
			return $arr;
		}	

		public function getPortletVideos($portletId){
			$arr = Array();
			foreach ($this->getPortletContent()['portletContent'] as $key => $value) {
					if($value->cd_name == 'Video' && $portletId == $value->portletId){
						$arr[] = $value;
					}
			}
			return $arr;
		}

		public function getPortletVideosBySearchKeywords($portletId){
			$arr = Array();
			foreach ($this->getPortletContent()['portletContent'] as $key => $value) {
					if($value->cd_name == 'Video' && $portletId == $value->portletId){
						$arr[] = $value;
					}
			}
			return $arr;
		}

		public function getPortletBestseller($portletId){
			$arr = Array();
			foreach ($this->getPortletContent()['portletContent'] as $key => $value) {
					if(($value->cd_name == 'Video' || $value->cd_name == 'Wallpaper' ) && $portletId == $value->portletId ){
						$arr[] = $value;
					}
			}
			return $arr;
		}

       

		public function getDifferentFileNames($fileUrl,$contentType,$resolution){
			$getAllFiles = explode(',',$fileUrl);
	        $tmpFile = explode('/',$getAllFiles[0]);
	        
	        if($contentType == 'Video' || $contentType == 'Bestsellers' ){
	        	   $getFileName = explode('_',$tmpFile[2]);
			       $fileName = $getFileName[0];
			       switch ($resolution) {
				       	case 'high':
	 							 $high = md5($fileName.'_640x320.mp4');			 
	 							 return $high;      		
	 							 break; 	
	 					case 'low':
	 							 $medium = md5($fileName.'_240x160.mp4');		 
	 							 return $medium;      		
	 							 break; 	
	 					case 'medium':
	 							 $low = md5($fileName.'_640x320.mp4');			 
	 							 return $low;      		
	 							 break;
				       	default:
				       		break;
			       }
	        }

	        if($contentType == 'Wallpaper'){
	        	$fileName = md5($tmpFile[2]);
	        	return $fileName;
	        }
	     
		}

		// public function getGenreName($genreId){
		// 	$genreName = getValuefromTable($this->dbCon, 'catalogue_detail', 'cd_id', 22);
		// 	return "GLAMOUR";
		// }

		// public function getUserStatus(){
		
				
		// 	return $this->userStatus;
		// }


		// public function getPromoId(){
		// 	return  'z_'.uniqid();
		// }

		public function contentPagination($arr,$startFrom,$eachPage){
			 $allContent = $arr;
			 $allContent = array_slice($allContent,$startFrom,$eachPage);
			 return $allContent;
		}
	}
?>