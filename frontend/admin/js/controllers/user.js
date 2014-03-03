app.controller('UserCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.roles = {};
    $scope.locations = {};
    $scope.selectedRoleOption = false;  // Will be set to the selected role option
    $scope.user = false;
    
    $("#navUsers a").focus();
    
    // If the user ID was passed in the URL, grab it.
    if($routeParams.id != undefined) {
        $scope.id = $routeParams.id;
    }
    
    $scope.load = function() {

        var loadUser = function($scope) {
            $http({
                method: 'GET',
                url: myndie.apiURL + "user/get/" + $scope.id
            }).success(function (data) {
                if(!data.status) {
                    alert(data.message);
                    return;
                }
                
                // Get the user from the data
                $scope.user = data.message; 
                
                // Check for the sharedRole node in the userdata
                if($scope.user.sharedRole.length == 1) {
                    var numUserRoles = utils.getObjectSize($scope.user.sharedRole);
                    if(numUserRoles == 1) {
                        var userRoleID = $scope.user.sharedRole[0].id;
                        
                        // Find the option in the roles array
                        for(var i in $scope.roles) {
                            if($scope.roles[i].id == userRoleID) {
                                $scope.selectedRoleOption = $scope.roles[i];    
                                break;
                            }
                        }
                        
                    }
                }
                
                // Does the user have any locations assigned?
                if($scope.user.sharedLocation) {
                    var numUserLocations = utils.getObjectSize($scope.user.sharedLocation);
                    
                    // Loop through the locations and check any should that the user is actually assigned to.
                    var numLocations = utils.getObjectSize($scope.locations);
                    for(l = 0; l < numLocations; l++) {
                        // Loop through the user locations looking for a match
                        for(ul = 0; ul < numUserLocations; ul++) {
                            if($scope.user.sharedLocation[ul].id == $scope.locations[l].id) {
                                $scope.locations[l].checked = true;
                                break;    
                            }    
                        } 
                    }                     
                    
                }                
            });
        }
        
        loadUser($scope);         
    }
    
    $scope.bindEvents = function() {
        
        /**
        * Handle the event when the user submits the main details form.
        */
        $("#frmUserDetails").submit(function(e) {
            e.preventDefault();

            $scope.save();
        });
        
        /**
        * Handle the event when the user submits the password reset form
        */
        $("#frmResetPassword").submit(function(e) {
            e.preventDefault();

            $scope.resetPassword();
        });  
        
        /**
        * Handle the event when the user submits the location assignment form
        */
        $("#frmLocations").submit(function(e) {
            e.preventDefault();

            $scope.saveLocations();
        });               
    }
    
    /**
    * Save the user
    * If the user ID is 0, a new user will be created, otherwise we simply update the current user
    */
    $scope.save = function() {
        // Set the user role
        var userRoleID = $scope.selectedRoleOption.id;
        $scope.user.roles = userRoleID;
        
        utils.hideMessages();   // Hide all message divs
        
        // Save the user
        var url =  myndie.apiURL + "user/save/" + $scope.id;

        $http.post(url, $scope.user).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new user, return to the main listing screen
            if($scope.id == 0) {
                $window.location.href = "#!/users";
                return;
            }
            
            utils.showSuccess("The user was updated successfully");
        });            
                    
    }
    
    /**
    * Processes a password reset request.
    */
    $scope.resetPassword = function() {

        utils.hideMessages();   // Hide all message divs
        
        // Save the user
        var url =  myndie.apiURL + "user/password_reset/" + $scope.id;

        $http.post(url, $scope.user).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }

            utils.showSuccess("The user's password was updated successfully");
        });                         
    } 
    
    /**
    * Saves the selected locations against the users record
    */
    $scope.saveLocations = function() {

        utils.hideMessages();   // Hide all message divs
        
        // Save the location assignments
        var url =  myndie.apiURL + "user/save_locations/" + $scope.id;
        
        // Build a CSV string of selected locations
        var selectedLocations = "";
        
        $("#frmLocations input[type='checkbox']:checked").each(function() {
            if(selectedLocations != "") {
                selectedLocations += ",";
            }
            
            selectedLocations += $(this).val(); 
        });
        
        var params = {};
        params["locations"] = selectedLocations;
        
        $http.post(url, params).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }

            utils.showSuccess("The user location assignments were updated successfully");
        });                         
    }       
    
    // Load the user roles
    globals.getRoles(function(roles) {
        $scope.roles = roles;
        
        // Load the list of locations
        $http.post(myndie.apiURL + "location/list", {}).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            $scope.locations = data.message;           
            
            // We're editing an existing user, load the user data
            if($scope.id > 0) {
                $scope.load();
            } else {
                // We're adding a new user.  
                // Reveal the password fields
                $("#newUserPasswordWrapper").removeClass("hidden");            
                $("#newUserPasswordWords input").attr("required", "required");
                
                // Hide anyting that should be hidden for new users
                $(".hideOnNew").addClass("hidden");
            }             
            
        });              
    });  
    
    $scope.bindEvents();   
}); 
