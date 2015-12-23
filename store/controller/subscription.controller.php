<?php

	class Subscription {
		public function __construct(){
	
			 include "../../../site/lib/bootstrap.php";
			 include "../../models/store.model.php";	
			 include  "../../../site/lib/functions.php";

				
			$dbCMS = new Db($config['Db']['icon_cms']['User'], $config['Db']['icon_cms']['Password'],$config['Db']['icon_cms']['Name']);

			$this->dbCon = $dbCMS->getConnection();
		
		}

		public function getPlanDetails($packageId){
			$result_packageids = getSubscriptionPlans($this->dbCon,$packageId);
			// print_r ($result_packageids);
			return $result_packageids;
		}
	}

?> 