    /*****************
    * Define any global Angular services here
    */
    
    // Add an automatic loading wheel into the Angular HTTP request object
    angular.module('SharedServices', [])
        .config(function ($httpProvider) {
            $httpProvider.responseInterceptors.push('myHttpInterceptor');
            var spinnerFunction = function (data, headersGetter) {
                // todo start the spinner here
                //alert('start spinner');
                $('#loading').show();
                return data;
            };
            $httpProvider.defaults.transformRequest.push(spinnerFunction);
        })
        // register the interceptor as a service, intercepts ALL angular ajax http calls
        .factory('myHttpInterceptor', function ($q, $window) {
            return function (promise) {
                return promise.then(function (response) {
                    // do something on success
                    // todo hide the spinner
                    //alert('stop spinner');
                    $('#loading').hide();
                    
                    if($window.location.href.indexOf("logout") <= 0) {
                        if(response.hasOwnProperty("data")) {
                            if(response.data.hasOwnProperty("message")) {
                                if((typeof response.data.message).toLowerCase() == "string") {
                                    if(response.data.message.indexOf("Invalid Session") > 0) {
                                        alert("Sorry your session has expired");
                                        $window.location.href = myndie.baseURL + "admin/#!logout";
                                        return false;
                                    }
                                }
                            }
                        }
                    }

                    return response;

                }, function (response) {
                    // do something on error
                    // todo hide the spinner
                    //alert('stop spinner');
                    $('#loading').hide();
                    return $q.reject(response);
                });
            };
        });
              