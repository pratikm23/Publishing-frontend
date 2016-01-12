<?php 
$cgImages = array(
 // 'http://dailymagic.in/unsubImages/ApexOS_Adrienne_pool.jpg',
 // 'http://dailymagic.in/unsubImages/GG_Cassie_bikini.jpg',
 // 'http://dailymagic.in/unsubImages/GG_Kayleigh_lingerie.jpg',
 // 'http://dailymagic.in/unsubImages/GG_Lindsey_bed.jpg',
 // 'http://dailymagic.in/unsubImages/Lizzie_wall.jpg'
	'https://lh6.ggpht.com/SkwnZx2duBQfzregu8_4PnBOnADfYG4VND1J6TfRTDl57a_vn4vOA_ZQqjqEJQ4pm9A_=h900'
);

$rand_keys = array_rand($cgImages);
 
$image_url = $cgImages[$rand_keys];
?>
<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
 <td align="center">
   <a href="direct2CG.php">
  <img  src="<?=$image_url?>?<?=$timestamp?>" width="400" height="125" alt=""/></a>
 </td>
</tr>
</table>