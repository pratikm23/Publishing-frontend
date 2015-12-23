<?php
class Config{
	public static function getConfig(){
		return array(
			'debug' => 1,
			'ValidOperators' => array('voda', 'idea', 'airtel', 'aircel'),
			'AllowedOperators' => array('voda', 'idea'),
			'Vendor' => array('Fire in the belly', 'key2connect'),
			'SiteLogo' => 'DM-logo',
			'Db' => array(
				'central' => array(
					'Name' => 'central_modules',
					'User' => 'central-user',
					'Password' => 'centr@l@123#'
				),
				'icon_cms' => array(
					'Name' => 'icon_cms',
					'User' => 'root',
					'Password' => ''
				),
				'ikon' => array(
					'Name' => 'ikon_cms',
					'User' => 'ikon',
					'Password' => 'ikon@123#'
				),
				'siteUser' => array(
					'Name' => 'siteuser',
					'User' => 'root',
					'Password' => ''
				),
				'plan' => array(
					'Name' => 'icon_plan',
					'User' => 'ikon',
					'Password' => 'ikon@123#'
				),
				'campaign' => array(
					'Name' => 'campaign_manager',
					'User' => 'cmadmin',
					'Password' => 'cm@dm!n'
				)
			),
			'CookieTag' => 'D2C',
			'Thumbnail' => array(
				'Width' => '100',
				'Height' => '100'
			),
			'BGW' => array(
				'Id' => '2',
				'AppId' => 'jet',
				'Uid' => 'jet',
				'Passwd' => 'jet@123',
				'Store' => 'jet',
				'OperatorConfig' => array(
					'voda' => array(
						'BillingServiceSub' => 'getvodabilling',
						'BillingServiceUnSub' => 'vodabilling',
						'DefaultPP' => 'JET0003',
						'Cmode' => 'WAP_D2C',
						'PPSet' => array('JET0001', 'JET0002', 'JET0003', 'JET0004', 'JET0005', 'JET0016')
					),
					'idea' => array(
						'BillingServiceSub' => 'getideabilling',
						'BillingServiceUnSub' => 'ideabilling',
						'DefaultPP' => 'JET0003',
						'Cmode' => 'WAP',
						'PPSet' => array('JET0001', 'JET0003', 'JET0005')
					),
					'airtel' => array(
						'BillingServiceSub' => '',
						'BillingServiceUnSub' => '',
						'DefaultPP' => '',
						'Cmode' => 'WAP',
						'PPSet' => array()
					),
					'aircel' => array(
						'BillingServiceSub' => '',
						'BillingServiceUnSub' => '',
						'DefaultPP' => '',
						'Cmode' => 'WAP',
						'PPSet' => array()
					)
				)	
			),
			'PPMapping' => array(
				'JET0001' => array(
					'Amount' => 45,
					'Duration' => '15 days'
				),
				'JET0002' => array(
					'Amount' => 30,
					'Duration' => '10 days'
				),
				'JET0003' => array(
					'Amount' => 21,
					'Duration' => 'weekly'
				),
				'JET0004' => array(
					'Amount' => 15,
					'Duration' => '5 days'
				),
				'JET0005' => array(
					'Amount' => 3,
					'Duration' => 'daily'
				),
				'JET0016' => array(
					'Amount' => 60,
					'Duration' => 'monthly'
				)
			),
			'SubscribeText' => 'Thank you for subscribing to DailyMagic. Now, you can download & enjoy unlimited content of your choice.',
			'Limits' => array(
				'Download' => 5
			),
			'PromoInterim' => array('2210203', '13313785', '23123950', '12257095', '2510117'),
			'CgImage' => array(
				'TopBanner' => '/cgImage/Header_cg_image_640x640.jpg',
				'BottomBanner' => '/cgImage/Footer_cg_image640x640.jpg'
			)	
		);
	}
}
?>