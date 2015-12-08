app.service('Page', ['$http', function ($http) {

<<<<<<< HEAD
    this.baseRestUrl = "http://192.168.3.182:3090";

=======
    this.baseRestUrl = "http://192.168.5.183:3090"; 
>>>>>>> 0abf3cd0801e9e6efdd78c04c6b8179bbeaea6ea


    this.getPortletContentByPageName = function(data,success){
        $http.post(this.baseRestUrl + '/getPortletContentByPageName',data).success(function (items) {
            success(items);
        });
    }

}]);