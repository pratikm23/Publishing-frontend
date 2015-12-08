<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="javascripts/angularjs/angular.js" ></script>
<!-- <script src="javascripts/ngProgress.min.js" ></script> -->
<!-- <script src="javascripts/jquery/dist/jquery.js" ></script> -->
<script src="javascripts/angular-bootstrap/ui-bootstrap-tpls.js" ></script>
<script src="javascripts/angular-ui-router/release/angular-ui-router.js" ></script>
<script src="javascripts/underscore/underscore.js" ></script>
<script src="public/scripts/app.js" ></script>
</head>
    <BODY>
		<div data-ng-app="myApp" data-ng-controller="portletsCtrl">
		<div style="text-align:center">
				<img src="public/assets/img/d2clogo_320x45.png" />
		</div>
		     <div data-ng-repeat="template in  templates">
	          <div data-ng-include src="template.url" ></div>
            </div>

		</div>

		<script>

		app.controller('portletsCtrl', function ($scope, $http,storeId,Page) {
				$scope.getPageName = function(variable){
                                        var query = window.location.search.substring(1);
                                        var vars = query.split('&');
                                        for (var i = 0; i < vars.length; i++) {
                                            var pair = vars[i].split('=');
                                            if (decodeURIComponent(pair[0]) == variable) {
                                                return decodeURIComponent(pair[1]);
                                            }
                                        }
            			}
		    var pdata = {
		        pageName : $scope.getPageName('pg'),
		        storeId : storeId
		    }
		    $scope.finalArray = {};


		    Page.getPortletContentByPageName(pdata,function(data){

		    	$scope.portletData = _.sortBy(data.portletData,'portletId');
		    	// $scope.portletWallpaper = _.sortBy( _.where(data, {cd_name: 'Wallpaper'} ),'portletId');
		    			    		
		    	$scope.templates = [];
		    	_.each(data.publishData, function(v,k){		    		
					_.each($scope.portletData, function(value,key){	
			    		if( _.has($scope.finalArray,v.portletId) ){
			    			$scope.finalArray[v.portletId].push(value);
			    		}else{
			    			$scope.finalArray[v.portletId] = [];
			    			console.log('v.portletId == value.portletId')
			    			console.log(v.portletId == value.portletId)
			    			if(v.portletId == value.portletId ){
								$scope.finalArray[v.portletId].push(value);
			    			
			    			}
			    			$scope.templates.push({
						        "name": "portlet"+v.portletId,
						        "url": "public/partials/portlets/portlet"+v.portletId+".html"
						    });
			    		}						 
			    	});
		    	})
    			console.log($scope.finalArray);
    			console.log($scope.templates);
			});
   		});
		</script>
		<script src="public/scripts/services/page.service.js"></script>
   </BODY>
</html>
