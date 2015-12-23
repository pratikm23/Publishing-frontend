<?php

//- turn off compression on the server
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 'Off');

// include 'config.inc.php';
$userId = 1011000;
$msisdn = 98567299090;

include 'lib/bootstrap.php';

// print_r($config);
// $_SESSION['downloadAllowed']=true;
// if (!isset($_SESSION['downloadAllowed'])){
// 	$redirectToCg =  $linkUrl.$SubParam.'f=home';
// 	header("Location: ".$redirectToCg);
// 	exit();
// }else{
// 	if($_SESSION['downloadAllowed'] == 'false'){
// 		$redirectToCg = $linkUrl.$SubParam.'f=home';
// 		header("Location: ".$redirectToCg);
// 		exit();
// 	}else{
		$fileType = $_GET['t'];
		$fileName = $_GET['n'];
		//Hardcode for testing purpose : 
		$fileName = "4ccbe2a8d8b95c0d375480b1e3a74b91";

		$catalogue_detail_id = $_GET['d'];
		$content_metadata_id = $_GET['m'];
		
		if( $fileType == md5('Wallpaper') ){
			// $filePath = '/var/www/dailymagic/ContentFiles/';
			$filePath = 'ContentFiles';
		}else{
			$filePath = 'video/';
		}
		
		$targetFile = '';
		foreach(glob($filePath.'/*.*') as $file) {
			
			if( md5(basename($file)) == $fileName ){
				$targetFile = basename($file);
			}
		}
		
		$file = $targetFile;
	
		if (file_exists($filePath.$file)) {
			if( $fileType == md5('Wallpaper') ){
								
				$extractFileVal = explode('_',$file);
				$fileVal = $extractFileVal[0];			
				$file_ext = pathinfo($file, PATHINFO_EXTENSION);	
				
				$templateId = $_GET['i'];
				
				// $Dimensions = getValuefromTable($db, 'content_template', 'ct_group_id', $templateId);
								
				/*
				for($i=0;$i<count($Dimensions);$i++){
					if( $Dimensions[$i]['ct_param_value'] == 'width' ){
						$WallpaperWidth = $Dimensions[$i]['ct_param'];
					}elseif( $Dimensions[$i]['ct_param_value'] == 'height' ){
						$WallpaperHeight = $Dimensions[$i]['ct_param'];
					}
				}
				*/	
				
				if( isset($mobileInfo['Wallpaper_Width']) and !empty($mobileInfo['Wallpaper_Width'])){
					$WallpaperWidth = $mobileInfo['Wallpaper_Width'];
					$WallpaperHeight = $mobileInfo['Wallpaper_Height'];
				}else{
					if( $mobileInfo['Resolution_Width'] > 800 ){
						$WallpaperWidth = '720';
						$WallpaperHeight = '1280';
					}else{
						if($mobileInfo['Resolution_Width'] == 800){
							if($mobileInfo['Resolution_Width'] == 800 and $mobileInfo['Resolution_Height'] == 1280){
								$WallpaperWidth = '720';
								$WallpaperHeight = '1280';
							}else{
								$WallpaperWidth = '800';
								$WallpaperHeight = '600';
							}
						}elseif($mobileInfo['Resolution_Width'] < 800 and $mobileInfo['Resolution_Width'] >= 768){
							$WallpaperWidth = '720';
							$WallpaperHeight = '1280';
						}else{
							// $WallpaperWidth = $mobileInfo['Resolution_Width'];
							$WallpaperWidth = 120;
							$WallpaperHeight = 160;
							// $WallpaperHeight = $mobileInfo['Resolution_Height'];
						}
					}
				}
				
				$file_name = $fileVal.'_'.$WallpaperWidth.'x'.$WallpaperHeight.'.'.$file_ext;
				
				$file_path = $filePath.$file_name;
				
				if (file_exists($file_path)) {		
					
					$dbSiteUser = new mysqli(DBHOST, $config['Db']['siteUser']['User'], $config['Db']['siteUser']['Password'], $config['Db']['siteUser']['Name']);
					
					if($dbSiteUser->connect_errno > 0){
						die('Unable to connect to database [' . $dbSiteUser->connect_error . ']');
					}
					
					$queryGetDownloadInfo = "select * from content_download where cd_cmd_id = ".$content_metadata_id." and cd_cd_id = ".$catalogue_detail_id." and cd_user_id = ".$userId." and cd_msisdn = ".$msisdn." and cd_app_id = 2";
					$checkDownloadInfo = $dbSiteUser->query($queryGetDownloadInfo);
					
					if($checkDownloadInfo->num_rows > 0 ){
						$data = $checkDownloadInfo->fetch_assoc();
						$downloadCount = $data['cd_download_count'] + 1;
						
						$queryUpdateDownloadInfo = "update content_download set cd_download_count = ".$downloadCount.", cd_download_date = NOW() where cd_cmd_id = ".$content_metadata_id." and cd_cd_id = ".$catalogue_detail_id." and cd_user_id = ".$userId." and cd_msisdn = ".$msisdn." and cd_app_id = 2 and cd_id = ".$data['cd_id'];
												
						$result = $dbSiteUser->query($queryUpdateDownloadInfo);
						
					}else{
						$queryInsertDownloadInfo = sprintf("insert into content_download(cd_user_id, cd_msisdn, cd_cmd_id, cd_download_count, cd_cd_id, cd_app_id, cd_download_date) values(%s, %s, %s, 1, %s, 2, NOW())",$userId, $msisdn, $content_metadata_id, $catalogue_detail_id );
						
						$result = $dbSiteUser->query($queryInsertDownloadInfo);
					}
					
					$dbSiteUser->close();
					/*
					$mime = mime_content_type($file_path);
					header('Content-type: ' . mime_content_type($file_path));

					header('Content-Disposition: attachment; filename="'.basename($file_path).'"');

					readfile($file_path);
					exit();
					*/
					
					//printData($file_path);
					//exit();
					
					startDownloading($file_path);
										
					
					//header('Content-Type: application/octet-stream');
					//header('Content-Disposition: attachment; filename='.basename($file_path));
					//header('Content-Transfer-Encoding: binary');
					//header('Expires: 0');
					//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					//header('Pragma: public');
					header('Content-Length: ' . filesize($file_path));
					//ob_clean();
					//flush();
					readfile($file_path);
					//exit;		
										
				}else{
					echo "No such file exists";
				}	
			}else{
				$dbSiteUser = new mysqli(DBHOST, $config['Db']['siteUser']['User'], $config['Db']['siteUser']['Password'], $config['Db']['siteUser']['Name']);
					
				if($dbSiteUser->connect_errno > 0){
					die('Unable to connect to database [' . $dbSiteUser->connect_error . ']');
				}
				
				$queryGetDownloadInfo = "select * from content_download where cd_cmd_id = ".$content_metadata_id." and cd_cd_id = ".$catalogue_detail_id." and cd_user_id = ".$userId." and cd_msisdn = ".$msisdn." and cd_app_id = 2";
				$checkDownloadInfo = $dbSiteUser->query($queryGetDownloadInfo);
				
				if($checkDownloadInfo->num_rows > 0 ){
					$data = $checkDownloadInfo->fetch_assoc();
					$downloadCount = $data['cd_download_count'] + 1;
					
					$queryUpdateDownloadInfo = "update content_download set cd_download_count = ".$downloadCount.", cd_download_date = NOW() where cd_cmd_id = ".$content_metadata_id." and cd_cd_id = ".$catalogue_detail_id." and cd_user_id = ".$userId." and cd_msisdn = ".$msisdn." and cd_app_id = 2 and cd_id = ".$data['cd_id'];
					
					$result = $dbSiteUser->query($queryUpdateDownloadInfo);
					
				}else{
					$queryInsertDownloadInfo = sprintf("insert into content_download(cd_user_id, cd_msisdn, cd_cmd_id, cd_download_count, cd_cd_id, cd_app_id, cd_download_date) values(%s, %s, %s, 1, %s, 2, NOW())",$userId, $msisdn, $content_metadata_id, $catalogue_detail_id );
					
					$result = $dbSiteUser->query($queryInsertDownloadInfo);
				}
				
				$dbSiteUser->close();
				
				$file_path = $filePath.$file;
				
				startDownloading($file_path);
				/*
				$mime = mime_content_type($file_path);
				header('Content-Type: "'.$mime.'"');
				header('Content-Disposition: attachment; filename='.basename($file_path));
				//header('Content-Length: ' . filesize($file_path));
				//ob_clean();
				//flush();
				readfile($file_path);
				exit();
				*/		
			}	
		}		
	// }
// }

function startDownloading($fullFilePath, $is_attachment = true){
	echo $fullFilePath;
	// make sure the file exists
	if (is_file($fullFilePath)){
		$file_size  = filesize($fullFilePath);
		$file = @fopen($fullFilePath,"rb");
		if ($file) {
			$file_name = basename($fullFilePath);		
			// set the headers, prevent caching
			header("Pragma: public");
			header("Expires: -1");
			header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
			// set appropriate headers for attachment or streamed file
			if ($is_attachment)
				header("Content-Disposition: attachment; filename=\"$file_name\"");
			else
				header('Content-Disposition: inline;');
	 
			// set the mime type based on extension, add yours if needed.
			$ctype_default = "application/octet-stream";
			$content_types = array(
				"mp3" => "audio/mpeg",
				"mpg" => "video/mpeg",
				"avi" => "video/x-msvideo",
				"gif" => "image/gif",
				"jpg" => "image/jpeg",
				"jpeg" => "image/jpeg",
				"png" => "image/png",
				"3gp" => "video/3gpp",
				"mp4" => "video/mp4"
			);
			$ctype = isset($content_types[$file_ext]) ? $content_types[$file_ext] : $ctype_default;
			header("Content-Type: " . $ctype);
			
			//check if http_range is sent by browser (or download manager)
			if(isset($_SERVER['HTTP_RANGE'])) {
				list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				if ($size_unit == 'bytes') {
					//multiple ranges could be specified at the same time, but for simplicity only serve the first range
					//http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
					list($range, $extra_ranges) = explode(',', $range_orig, 2);
				}else{
					$range = '';
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					exit;
				}
			}else{
				$range = '';
			}
			
			//figure out download piece from range (if set)
			list($seek_start, $seek_end) = explode('-', $range, 2);
			
			//set start and end based on range (if set), else set defaults
			//also check for invalid ranges.
			$seek_end   = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)),($file_size - 1));
			$seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);
	 
			//Only send partial content header if downloading a piece of the file (IE workaround)
			if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
				header('HTTP/1.1 206 Partial Content');
				header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
				header('Content-Length: '.($seek_end - $seek_start + 1));
			}else
				header("Content-Length: $file_size");
	 
			header('Accept-Ranges: bytes');
	 
			set_time_limit(0);
			fseek($file, $seek_start);
	 
			while(!feof($file)) {
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
				if (connection_status()!=0) {
					@fclose($file);
					exit;
				}			
			}
	 
			// file save was a success
			@fclose($file);
			exit;
		}
	}else{
		
	}
}
?>