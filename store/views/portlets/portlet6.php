<?php 
	$cgImages = array(
		'https://lh6.ggpht.com/SkwnZx2duBQfzregu8_4PnBOnADfYG4VND1J6TfRTDl57a_vn4vOA_ZQqjqEJQ4pm9A_=h900'
	);
	$rand_keys = array_rand($cgImages);
	 
	$image_url = $cgImages[$rand_keys];
?>
	<table width="90%"  border="0" style="margin-top:20px" align="center" cellpadding="0" cellspacing="0">
	<tr>
	 <td align="center">
	   <a href="subscriptionplans.php">
	  <img  src="<?=$image_url?>?<?=$timestamp?>" width="400" height="125" alt=""/></a>
	 </td>
	</tr>
</table>