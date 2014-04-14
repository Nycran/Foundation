app.controller('CategoryCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.category = false;
    
    $("#navCategories a").focus();
 
    
    // If the category ID was passed in the URL, grab it.
    if($routeParams.id != undefined) {
        $scope.id = $routeParams.id;
    }
    
    $scope.load = function() {
        $http({
            method: 'GET',
            url: myndie.apiURL + "category/get/" + $scope.id
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            // Get the category from the data
            $scope.category = data.message; 
        });      
    }
    
    $scope.bindEvents = function() {
        
        /**
        * Handle the event when the user submits the category details form.
        */
        $("#frmDetails").submit(function(e) {
            e.preventDefault();

            $scope.save();
            
        });        
    }
    
    /**
    * Save the category
    * If the category ID is 0, a new category will be created, otherwise we update the current category
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs
        
        // Save the category
        var url =  myndie.apiURL + "category/save/" + $scope.id;

        $http.post(url, $scope.category).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new category, return to the main listing screen
            if($scope.id == 0) {
                $window.location.href = "#!/categories";
                return;
            }
            
            utils.showSuccess("The category was updated successfully");
        });            
                    
    }

    // We're editing an existing category, load the category data
    if($scope.id > 0) {
        $scope.load();
    } else {
        // We're adding a new category.
        $scope.category = { enabled: true }; 
    }           
    
    $scope.bindEvents();   
}); 
