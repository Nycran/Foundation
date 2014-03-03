app.controller('LocationCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.location = false;
    
    $("#navLocations a").focus();
 
    
    // If the location ID was passed in the URL, grab it.
    if($routeParams.id != undefined) {
        $scope.id = $routeParams.id;
    }
    
    $scope.load = function() {
        $http({
            method: 'GET',
            url: myndie.apiURL + "location/get/" + $scope.id
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            // Get the location from the data
            $scope.location = data.message; 
        });      
    }
    
    $scope.bindEvents = function() {
        
        /**
        * Handle the event when the user submits the location details form.
        */
        $("#frmDetails").submit(function(e) {
            e.preventDefault();

            $scope.save();
            
        });        
    }
    
    /**
    * Save the location
    * If the location ID is 0, a new location will be created, otherwise we simply update the current location
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs
        
        // Save the location
        var url =  myndie.apiURL + "location/save/" + $scope.id;

        $http.post(url, $scope.location).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new location, return to the main listing screen
            if($scope.id == 0) {
                $window.location.href = "#!/locations";
                return;
            }
            
            utils.showSuccess("The location was updated successfully");
        });            
                    
    }

    // We're editing an existing location, load the location data
    if($scope.id > 0) {
        $scope.load();
    } else {
        // We're adding a new location. 
    }           
    
    $scope.bindEvents();   
}); 
