app.controller('ArticleCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.article = false;
    
    $("#navArticles a").focus();

    
    // If the article ID was passed in the URL, grab it.
    if($routeParams.id != undefined) {
        $scope.id = $routeParams.id;
    }
    
    $scope.load = function() {
        $http({
            method: 'GET',
            url: myndie.apiURL + "article/get/" + $scope.id
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            // Get the article from the data
            $scope.article = data.message; 
        });      
    }
    
    $scope.bindEvents = function() {
        
        /**
        * Handle the event when the user submits the article details form.
        */
        $("#frmDetails").submit(function(e) {
            e.preventDefault();

            $scope.save();
            
        });        
    }
    
    /**
    * Save the article
    * If the article ID is 0, a new article will be created, otherwise we update the current article
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs
        
        // Save the article
        var url =  myndie.apiURL + "article/save/" + $scope.id;

        $http.post(url, $scope.article).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new article, return to the main listing screen
            if($scope.id == 0) {
                $window.location.href = "#!/articles";
                return;
            }
            
            utils.showSuccess("The article was updated successfully");
        });            
                    
    }

    // We're editing an existing article, load the article data
    if($scope.id > 0) {
        $scope.load();
    } else {
        // We're adding a new article. 
    }           
    
    $scope.bindEvents();   
}); 
