app.controller('LogoutCtrl', function ($scope, $http, $window) {
    var self = this;  

    $http({
        method: 'POST',
        url: myndie.apiURL + "user/logout",
        data: {},
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    }).success(function (data) {

        $window.location.href = myndie.baseURL + "admin/";    
    }); 
}); 
