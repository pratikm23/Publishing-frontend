<!-- VIDEO INTERNAL PAGE-->
<?php
    // Portlet Config : 
    $CURRENT_PORTLETID = 9;
    $PORTLET_CONTENT_TYPE = 'Video';
    $PORTLET_RESOLUTION = 'low';
    $THUMBNAIL_LIMIT = 10;
    $EACHPAGE = 4; //IN each page how many content will be displayed.
?>

<div style="height: 30px;
    background: #ccc;
     padding-top: 6px;">
    <h5 style="margin-top: 7px;">Videos</h5>
</div>

<table width="70%" style="margin-top:20px" border="0" align="center" cellpadding="0" cellspacing="0">
	    <tr>
<?php 
	 $i = 0;
     $startFrom = 0 * $EACHPAGE;

     //FOR PAGINATION
     if(isset($_GET['startFrom'])){
         $startFrom = $_GET['startFrom'] * $EACHPAGE;
     }

      //FOR PAGINATION
      $allVideos= $storeObj->contentPagination($storeObj->getPortletVideos($CURRENT_PORTLETID),$startFrom,$EACHPAGE);
     
     
     if($startFrom == 0){
        $startFrom = 1; //For next iteration
     }


	 foreach ($allVideos as $key => $value) {
	 	if(++$i > $THUMBNAIL_LIMIT) break; //For restricting thumbnails.
            if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
?>		
        <td align="center">
            <a href="../<?=$SUBPARAM?>&f=home&t=<?=$value->contentTypeMD5?>&n=<?=base64_encode($storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION))?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>">
            	<img src="http://dailymagic.in<?=$value->cft_thumbnail_img_browse?>?<?=$value->timestamp?>" width="125" height="125" alt="" />
            </a>
            <br />
            <?php echo $value->genre; ?>
        </td>
<?php
        }else{
            //If user is subscribed :  clicking on thumbnail for video will download the same in low res.
?>
    
         <td align="center">
            <a href="<?=$DOWNLOADPATH?>?t=<?=$value->contentTypeMD5?>&n=<?=$storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION)?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>">
                    <img src="http://dailymagic.in<?=$value->cft_thumbnail_img_browse?>?<?=$value->timestamp?>" width="125" height="125" alt="" /></a>
            <br/>
           
            <!-- Links for medium and high res -->
            <a href="<?=$DOWNLOADPATH?>?t=<?=$value->contentTypeMD5?>&n=<?=$storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,'high')?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>">High</a>
            <a href="<?=$DOWNLOADPATH?>?t=<?=$value->contentTypeMD5?>&n=<?=$storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,'medium')?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>">Medium</a>
        </td>
        <td> 
             <?php  echo "Category:".$value->genre."<br/>"; ?> 
        </td>
 <?php
            }//else
            echo "</tr>";
		} // foreach
?>

    <tr >
        <td height="30" colspan="3" align="right">
            <?php 
            //TO CHANGE :: PROMO ID     
                  if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){                  
            ?>
                <a href="../<?=$SUBPARAM?>&f=home" style="text-decoration:none;">More >></a>
            <?php
                    }else{
            ?>
                 <a href="?pg=video.php" style="text-decoration:none;">More >></a>
            <?php

                    }
            ?>

        </td>
    </tr>
</table>

