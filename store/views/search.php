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
<?php
	include_once "../controller/search.controller.php";
	$searchObj = new Search();
			
	//STORE CONFIGS : 
	// $CURRENT_PORTLETID = 3;
    // $PORTLET_CONTENT_TYPE = 'Video';
    $PORTLET_RESOLUTION = 'low';
    $THUMBNAIL_LIMIT = 2;

	$STOREID = 1;
	$MAINPATH = $_SERVER['DOCUMENT_ROOT']."PortletPublish_php";
	// $DOWNLOADPATH =  $MAINPATH."site/download.php";
	$DOWNLOADPATH =  "download_cloud.php";
	$THUMBURL = "http://d85mhbly9q6nd.cloudfront.net/";
	
	$searchObj->setStoreConfigs($STOREID);

	
	$USERSTATUS = $searchObj->userStatus;
	$USERSTATUS = "SUBSCRIBED";

	 // $USERSTATUS = $storeObj->$userStatus;
	 
	$PROMOID = $searchObj->promoId;
	$LINKURL = $searchObj->linkUrl;
	$SUBPARAM =$searchObj->subParam;
	//$SEARCHTXT = isset($_GET['search_txt']) ? $_GET['search_txt'] :'';
	$SEARCHTXT = "a";
?>
	<div style="text-align:center">
			<img src="../../public/assets/img/d2clogo_320x45.png" />
			<?php require_once "portlets/header.php" ?>
	</div>
	<?php
		// $searchObj->getSearchContent('babe');

		$portletArray_video = $searchObj->getPortletFilteredContent($SEARCHTXT,9);
		$portletArray_wallpaper = $searchObj->getPortletFilteredContent($SEARCHTXT,8);
	?>

	<div style="height: 30px;
	    background: #ccc;
	     padding-top: 6px;">
	    <h5 style="margin-top: 7px;">Videos</h5>
	</div>
	<table width="90%" style="margin-top:20px" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
	<?php 
		 $i = 0;
		 foreach ($portletArray_video as $key => $value) {

		 	if(++$i > $THUMBNAIL_LIMIT) break; //For restricting thumbnails.
	            if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
	?>		
	        <td align="center">
	            <a href="../<?=$SUBPARAM?>&f=home&t=<?=$value['contentTypeMD5']?>&m=<?=$value['cft_cm_id']?>&d=<?=$value['parentId']?>">
						<img src="<?=$THUMBURL?><?=$value['cft_cm_id']?>_thumb_125_125.jpg" width="125" height="125" alt="" /></a>            <br />
	            <?php
	                echo $value['genre']; 
	            ?>
	        </td>
	<?php
	        }else{
	            //If user is subscribed :  clicking on thumbnail for video will download the same in low res.
	?>
	    
	         <td align="center">
	            <a href="<?=$DOWNLOADPATH?>?t=<?=$value['contentTypeMD5']?>&m=<?=$value['cft_cm_id']?>&d=<?=$value['parentId']?>">
	                    <img src="<?=$THUMBURL?><?=$value['cft_cm_id']?>_thumb_125_125.jpg" width="125" height="125" alt="" /></a>
	            <br/>
	            <?php echo $value['genre']."<br/>"; ?>
	            <!-- Links for medium and high res -->
	           
	            <a href="<?=$DOWNLOADPATH?>?t=<?=$value['contentTypeMD5']?>&m=<?=$value['cft_cm_id']?>&d=<?=$value['parentId']?>">High</a>
	            <a href="<?=$DOWNLOADPATH?>?t=<?=$value['contentTypeMD5']?>&m=<?=$value['cft_cm_id']?>&d=<?=$value['parentId']?>">Medium</a>
	        </td>
	 <?php
	            }//else
			} // foreach
	?>
	    </tr>
	    <tr>
	        <td height="30" colspan="3" align="right">
	            <?php 
	            //TO CHANGE :: PROMO ID     
	                  if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){                  
	            ?>
	                <a href="../<?=$SUBPARAM?>&f=home" style="text-decoration:none;">More >></a>
	            <?php
	                    }else{
	            ?>
	                 <a href="store.php?pg=video.php" style="text-decoration:none;">More >></a>
	            <?php

	                    }
	            ?>

	        </td>
    	</tr>
	</table>
	<div style="height: 30px;
	    background: #ccc;
	     padding-top: 6px;">
	    <h5 style="margin-top: 7px;">Photos</h5>
	</div>
	<table width="90%" style="margin-top:20px" border="0" align="center" cellpadding="0" cellspacing="0">
	    <tr>
		<?php 
			 $i = 0;
			 foreach ($portletArray_wallpaper as $key => $value) {
			 	// print_r($portletArray_wallpaper);
			 	// print_r($value);
			 	if(++$i > $THUMBNAIL_LIMIT) break; //For restricting thumbnails.
		            if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
		     
		?>		
		        <td align="center">
		            <a href="../<?=$SUBPARAM?>&f=home&t=<?=$value['contentTypeMD5']?>&m=<?=$value['cft_cm_id']?>&d=<?=$value['parentId']?>&i=<?=$value['cf_template_id']?>">
		            	<img src="<?=$THUMBURL?><?=$value['cft_cm_id']?>_thumb_125_125.jpg" width="125" height="125" alt="" /></a>
		            <br />
		        </td>
		<?php
		            }else{
		?>
		            <td align="center">
		                <a href="<?=$DOWNLOADPATH?>?&t=<?=$value['contentTypeMD5']?>&m=<?=$value['cf_cm_id']?>&d=<?=$value['parentId']?>&i=<?=$value['cf_template_id']?>">
		                  <img src="<?=$THUMBURL?><?=$value['cft_cm_id']?>_thumb_125_125.jpg" width="125" height="125" alt="" /></a>   
		                <br />
		             </td>
		<?php
		            }
				}
		?>
		    </tr>
		    <tr >
		        <td height="30" colspan="3" align="right">
		            <?php
		                  if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
		            ?>
		                <a href="../<?=$SUBPARAM?>&f=home" style="text-decoration:none;">More >></a>
		            <?php
		                }else{

		            ?>
		                 <a href="store.php?pg=photos.php" style="text-decoration:none;">More >></a>
		            <?php
		                }
		            ?>
		        </td>
		    </tr>
		</table>
 	
 </body>
 </html>