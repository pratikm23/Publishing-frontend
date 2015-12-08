<html>
<head>
<script src="javascripts/angularjs/angular.js" ></script>
<!-- <script src="javascripts/ngProgress.min.js" ></script> -->
<!-- <script src="javascripts/jquery/dist/jquery.js" ></script> -->
<script src="javascripts/angular-bootstrap/ui-bootstrap-tpls.js" ></script>
<script src="javascripts/angular-ui-router/release/angular-ui-router.js" ></script>
<script src="javascripts/underscore/underscore.js" ></script>
</head>
    <BODY>
		<div ng-app="myApp" ng-controller="portletsCtrl"> 

			<div ng-repeat="template in  templates">
	          <div ng-include src="template.url" ></div>
            </div>

		</div>

		<script>
		var app = angular.module('myApp', []);
		app.controller('portletsCtrl', function ($scope, $http,  Page) {
		    var pdata = {
		        pageName : "home.php"
		    }

		    $scope.finalArray = {};


		    Page.getPortletContentByPageName(pdata,function(data){

		    	$scope.portletData = _.sortBy(data,'portletId');
		    	// $scope.portletWallpaper = _.sortBy( _.where(data, {cd_name: 'Wallpaper'} ),'portletId');
		    	$scope.templates = [];
		    	_.each($scope.portletData, function(value,key){
		    		console.log(value.portletId);
		    		if( _.has($scope.finalArray,value.portletId) ){
		    			$scope.finalArray[value.portletId].push(value);
		    		}else{
		    			$scope.finalArray[value.portletId] = [];
		    			$scope.finalArray[value.portletId].push(value);
		    			$scope.templates.push({
					        "name": "portlet"+value.portletId,
					        "url": "public/partials/portlets/portlet"+value.portletId+".html"
					    });
		    		}
				    // debugger;
					// console.log(data);
		    	});
    			console.log($scope.finalArray);
    			console.log($scope.templates);
			});
   		});
		</script>
		<script src="public/scripts/services/page.service.js"></script>
   </BODY>
</html>
