<div style="text-align:center">
				<img src="../../public/assets/img/d2clogo_320x45.png" />
</div>
<?php
	include_once '../../preload/Store/config.php';	
    include_once "../../site/lib/functions.php";
	include "../controller/subscription.controller.php";
	$subObj = new Subscription();
	$USERSTATUS = $userStatus;
	include 'portlets/header.php';

?>

		<center><strong>Subscription Packs</strong></center>
<!-- <div style="width:50%"> -->
			<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
				<?php 
					$subscriptionPlans = $subObj->getPlanDetails();

					if(empty($subscriptionPlans->subscriptionDetails)){
						echo "<center>No Subscription plan found</center>";
						exit;
					}

					if($subscriptionPlans->status != 'ERROR' ){
						if( !empty( $subscriptionPlans->subscriptionDetails ) ) {
							foreach ($subscriptionPlans->subscriptionDetails as $subscriptionDetail ) {
												
						?>
						 
		<tr>
     			<td>&nbsp;</td>
   		</tr>
	   <tr>
	     <td >
	     <table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td>
	    	<!-- $value['sp_jed_id'] -->
	    	<a href="CallSubscription.php?EventId=<?=base64_encode($subscriptionDetail->sp_jed_id)?>" style=" text-decoration:none; color:#000000;"> 
		
		    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
				       <tr>
				         <td width="10" align="center" bgcolor="#999999"></td>
				         <td height="10" align="center" bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				       </tr>
				       <tr>
				         <td align="center" bgcolor="#999999">&nbsp;</td>
				         <td width="60" height="60" align="center" bgcolor="#CCCCCC"><?php echo $subscriptionDetail->pss_sp_id ?></td>
				         <td width="10" bgcolor="#999999">&nbsp;</td>
				         <td bgcolor="#999999"><?=$subscriptionDetail->sp_plan_name?><br />
				           <?=$subscriptionDetail->sp_description?></td>
				       </tr>
				       <tr>
				         <td align="center" bgcolor="#999999"></td>
				         <td height="10" align="center" bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				       </tr>
		    	 </table>
		    </a>
	 	</td>
  </tr>
  </table>

	
				<?php
					
					}

				}else {?>
					<tr>
				         <td align="center" bgcolor="#999999"><?php "No subscription plans found !." ?></td>
				         
				    </tr>
				<?php }
			}else{ ?>
					<tr>
				         <td align="center" bgcolor="#999999"><?php echo $subscriptionPlans->msgs; ?></td>
				         
				    </tr>
			 <?php 	} ?>
	    
</table>
	<center>Tip: Click on one of the options above to subscribe</center>
<!-- </div> -->
<div >
	<?php include_once ('portlets/footer.php'); ?>
</div>

