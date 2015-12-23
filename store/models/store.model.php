<?php
	// class storeModel {
	// 	function __construct($con){
	// 		$this->$con = $con;
	// 	}

	// 	public function getStoreDetails(){
	// 		$sql = "SELECT * FROM icn_store";
	// 		return $con->query($sql);
	// 	}
	// }

	function getStoreDetails($con){
		$sql = "SELECT * FROM icn_store";
		return $con->query($sql);
	}
	

	function getPackageIdsByPageName($con, $pageName, $storeId ){
		$sql = "SELECT pub_map.pmpp_sp_pkg_id as packageId ,portlet.ppp_id as portletId, pub_map.pmpp_id as portletMapId FROM icn_pub_map_portlet_pkg  AS pub_map "
			."Right OUTER JOIN icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id ) "
			."Right OUTER JOIN icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id ) "
			."WHERE pub.pp_page_file = '".$pageName."' AND pub.pp_sp_st_id = ".$storeId
			." AND portlet.ppp_is_active = 1 AND ISNULL( portlet.ppp_crud_isactive ) "
			."AND ISNULL( pub.pp_crud_isactive ) AND ISNULL( pub_map.pmpp_crud_isactive ) AND portlet.ppp_pkg_allow >= 0 ORDER BY portlet.ppp_id,pub_map.pmpp_id ";
		return $con->query($sql);			
	}

	function getPortletContentByPackageId( $con, $packageIds ){
		$packageIds = implode(",",$packageIds);
		//Current query : 
		 $sql = "SELECT DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') as timestamp, "
			."  SUBSTR(CONCAT('z_',MD5(RAND()),MD5(RAND())),1,32) as promoid, "
			." md5(cd.cd_name) as contentTypeMD5, md5(cf.cf_url_base) as contentFileURLMD5 ,pub_map.pmpp_ppp_id as  portletId,cft.cft_thumbnail_img_browse,cft.cft_thumbnail_size,cmd.cm_title,cmd.cm_genre,cd.cd_id,cd.cd_name, cf.cf_url ,cf.cf_cm_id,cf.cf_template_id,sp.sp_pkg_id FROM  icn_store_package AS sp "
			." JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id ) "
			." JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id "
			." JOIN icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id "
			." JOIN icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id "
			." JOIN content_files AS cf ON cf.cf_cm_id = pc.pc_cm_id "
			." JOIN content_files_thumbnail AS cft ON cft.cft_cm_id = pc.pc_cm_id "
			." JOIN content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id "
			." JOIN catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type "
			."  WHERE sp.sp_pkg_id IN (".$packageIds.") AND ippp.ppp_crud_isactive IS NULL  AND cmd.cm_state = 4  AND cmd.cm_starts_from <= NOW() AND cmd.cm_expires_on >= NOW()  group by portletId,cf.cf_cm_id "
			." Order By portletId,cf.cf_cm_id ";
		return $con->query($sql);

	}

function getPortletSearch($con, $pageName, $storeId ){
		$sql = "SELECT pub_map.pmpp_sp_pkg_id as packageId ,portlet.ppp_id as portletId, pub_map.pmpp_id as portletMapId FROM icn_pub_map_portlet_pkg  AS pub_map "
			."Right OUTER JOIN icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id ) "
			."Right OUTER JOIN icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id ) "
			."WHERE pub.pp_page_file = '".$pageName."' AND pub.pp_sp_st_id = ".$storeId
			." AND portlet.ppp_is_active = 1 AND ISNULL( portlet.ppp_crud_isactive ) "
			."AND ISNULL( pub.pp_crud_isactive ) AND ISNULL( pub_map.pmpp_crud_isactive ) AND portlet.ppp_pkg_allow >= 0 ORDER BY portlet.ppp_id,pub_map.pmpp_id ";
		return $con->query($sql);			
	}

function getSubscriptionPlans($con, $packageId){
		$sql = "SELECT pss_sp_id,sp_plan_name,sp_caption,sp_description,sp_jed_id
				FROM  icn_package_subscription_site, icn_sub_plan 
				WHERE icn_package_subscription_site.pss_sp_pkg_id = $packageId  
				AND ISNULL( icn_package_subscription_site.pss_crud_isactive ) 
				AND icn_package_subscription_site.pss_sp_id = icn_sub_plan.sp_id";
		return $con->query($sql);			
	}
?>