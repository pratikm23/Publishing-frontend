<?php
    // Portlet Config : 
    $CURRENT_PORTLETID = 4;
    $PORTLET_CONTENT_TYPE = 'Wallpaper';
    $PORTLET_RESOLUTION = '';
    $THUMBNAIL_LIMIT = 2;
?>

<div style="height: 30px;
    background: #ccc;
     padding-top: 6px;">
    <h5 style="margin-top: 7px;">Photos</h5>
</div>

<table width="90%" style="margin-top:20px" border="0" align="center" cellpadding="0" cellspacing="0">
	    <tr>
<?php 
	 $i = 0;
	 foreach ($storeObj->getPortletWallpapers($CURRENT_PORTLETID) as $key => $value) {
	 	if(++$i > $THUMBNAIL_LIMIT) break; //For restricting thumbnails.
            if($USERSTATUS == 'NEWUSER' || $USERSTATUS == 'UNKNOWN' || $USERSTATUS == 'UNSUBSCRIBED' ){
?>		
        <td align="center">
            <a href="http://dailymagic.in/direct2Cg.php?c=1&promo=<?=$PROMOID?>&f=home&t=<?=$value->contentTypeMD5?>&n=<?=base64_encode($storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION))?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>&i=<?=$value->cf_template_id?>">
            	<img src="http://dailymagic.in<?=$value->cft_thumbnail_img_browse?>?<?=$value->timestamp?>" width="125" height="125" alt="" /></a>
            <br />
        </td>
<?php
            }else{
?>
            <td align="center">
                <a href="<?=$DOWNLOADPATH?>?&t=<?=$value->contentTypeMD5?>&n=<?=$storeObj->getDifferentFileNames($value->cf_url,$PORTLET_CONTENT_TYPE,$PORTLET_RESOLUTION)?>&m=<?=$value->cf_cm_id?>&d=<?=$value->cd_id?>&i=<?=$value->cf_template_id?>">
                    <img src="http://dailymagic.in<?=$value->cft_thumbnail_img_browse?>?<?=$value->timestamp?>" width="125" height="125" alt="" /></a>
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
                <a href="http://dailymagic.in/direct2Cg.php?c=1&promo=<?=$value['promoid']?>&f=home" style="text-decoration:none;">More >></a>
            <?php
                }else{

            ?>
                 <a href="?pg=photos.php" style="text-decoration:none;">More >></a>
            <?php
                }
            ?>
        </td>
    </tr>
</table>

