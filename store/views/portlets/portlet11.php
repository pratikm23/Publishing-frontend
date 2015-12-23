<!-- Internal Wallpaper more page -->
<?php
    // Portlet Config : 
    $CURRENT_PORTLETID = 11;
    $PORTLET_CONTENT_TYPE = 'Wallpaper';
    $PORTLET_RESOLUTION = '';
    $THUMBNAIL_LIMIT = 10;
    $EACHPAGE = 4; //IN each page how many content will be displayed.
?>

<div style="height: 30px;
    background: #ccc;
     padding-top: 6px;">
    <h5 style="margin-top: 7px;">Photos</h5>
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
    $allWallpapers = $storeObj->contentPagination($storeObj->getPortletWallpapers($CURRENT_PORTLETID),$startFrom,$EACHPAGE);
     
     
     if($startFrom == 0){
        $startFrom = 1; //For next iteration
     }
       foreach ( $allWallpapers as $key => $value ) {
        if(++$i > $THUMBNAIL_LIMIT) break; //For restricting thumbnails.
            if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
	          
?>		
        <td align="center">
            <a href="http://dailymagic.in/direct2Cg.php?c=1&promo=<?=$PROMOID?>&f=home&t=<?=$value['contentTypeMD5']?>&n=<?=base64_encode($storeObj->getDifferentFileNames($value['cf_url'],$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION))?>&m=<?=$value['cf_cm_id']?>&d=<?=$value['cd_id']?>&i=<?=$value['cf_template_id']?>">
            	<img src="http://dailymagic.in<?=$value['cft_thumbnail_img_browse']?>?<?=$value['timestamp']?>" width="125" height="125" alt="" /></a>
            <br />
        </td>
<?php
      }else{
       
?>

            <td align="center" style="padding:0 8px 4px 0;">
                <a href="<?=$DOWNLOADPATH?>?&t=<?=$value->contentTypeMD5?>&n=<?=$storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION)?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>&i=<?=$value->cf_template_id?>">
                    <img src="http://dailymagic.in<?=$value->cft_thumbnail_img_browse?>" width="125" height="125" alt="" /></a>
                    <!-- <img src="http://media02.hongkiat.com/ww-flower-wallpapers/roundflower.jpg" width="125" height="125" alt="" /></a> -->
                <br />
             </td>
<?php
            }
            //For showing only two td in each row.
            if($i%2 == 0){
                         echo "</tr>";
             }
		}
?>
    </tr>
    <tr >
        <td height="30" colspan="3" align="right">
            <?php
                  if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
            ?>
                <a href="http://dailymagic.in/direct2Cg.php?c=1&promo=<?=$value['promoid']?>&f=home" style="text-decoration:none;">More >></a>
            <?php
                }else{

            ?>
                 <a href="?pg=photos.php&startFrom=<?=$startFrom?>" style="text-decoration:none;">More >></a>
            <?php
                }
            ?>
        </td>
    </tr>
</table>

