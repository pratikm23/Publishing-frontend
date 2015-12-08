app.service('Page', ['$http', function ($http) {

    this.baseRestUrl = "http://192.168.3.182:3090";



    this.getPortletContentByPageName = function(data,success){
        $http.post(this.baseRestUrl + '/getPortletContentByPageName',data).success(function (items) {
            success(items);
        });
    }

}]);