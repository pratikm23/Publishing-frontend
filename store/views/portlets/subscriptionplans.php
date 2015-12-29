<?php
	require_once "../../controller/subscription.controller.php";
	// require_once "../../../preload/Store/config.php";
	$subObj = new Subscription();
	$PACKAGEID = 11;

// $dbPlan = new mysqli(DBHOST, $config['Db']['plan']['User'], $config['Db']['plan']['Password'], $config['Db']['plan']['Name']);

// if($dbPlan->connect_errno > 0){
//     die('Unable to connect to database [' . $dbPlan->connect_error . ']');
// }

// $query = "select * from site_sub_plan order by ssp_vendor_id asc";
// $GetAllPlans = $dbPlan->query($query);

// $packs = array();

// while($row = $GetAllPlans->fetch_assoc()){
// 	$packs[] = $row;
// }

?>
		<center><strong>Subscription Packs</strong></center>
	<!-- <tr>
		<td height="30"><strong>Subscription Packs</strong></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr> -->
		<!-- <td align="center"> -->
			<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
				<?php 
					foreach ($subObj->getPlanDetails($PACKAGEID) as $key => $value) {
				?>
						<!-- <tr>
							<td>
								<a href="../CallSubscription.php?EventId=<?=base64_encode($value['sp_jed_id'])?>" style=" text-decoration:none; color:#000000;"> 
								<?php /* <a href="#" style=" text-decoration:none; color:#000000;">*/ ?>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="10" align="center" bgcolor="#999999"></td>
											<td height="10" align="center" bgcolor="#999999"></td>
											<td bgcolor="#999999"></td>
											<td bgcolor="#999999"></td>
										</tr>								
										<tr>
											<td align="center" bgcolor="#999999">&nbsp;</td>
											<td width="60" height="60" align="center" bgcolor="#CCCCCC"><?=$value['sp_plan_name']?></td>
											<td width="10" bgcolor="#999999">&nbsp;</td>
											<td bgcolor="#999999"><?=$value['sp_caption']?><br />
												<?=$value['sp_description']?>
											</td>											
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
						</tr> -->
		<tr>
     			<td>&nbsp;</td>
   			</tr>
   <tr>
     <td align="center">
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  	
	    <td>
	    	<!-- $value['sp_jed_id'] -->
	    	<a href="../CallSubscription.php?EventId=<?=base64_encode('JET0002')?>" style=" text-decoration:none; color:#000000;"> 
		
		    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
				       <tr>
				         <td width="10" align="center" bgcolor="#999999"></td>
				         <td height="10" align="center" bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				         <td bgcolor="#999999"></td>
				       </tr>
				       <tr>
				         <td align="center" bgcolor="#999999">&nbsp;</td>
				         <td width="60" height="60" align="center" bgcolor="#CCCCCC">99</td>
				         <td width="10" bgcolor="#999999">&nbsp;</td>
				         <td bgcolor="#999999"><?=$value['sp_plan_name']?><br />
				           <?=$value['sp_description']?></td>
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

				<?php
					# code...
								// for($i=0;$i<count($packs);$i++){ 
				// 	if( !in_array($packs[$i]['ssp_bgw_ef_id'], $config['BGW']['OperatorConfig'][$operator]['PPSet']) ){
				// 		// do nothing
				// 	}else{
						// print_r($value);
					}
				?>
			
	</table>
		<!-- </td> -->
	<!-- </tr> -->
