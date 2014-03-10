app.controller('ArticleCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.article = false;
    $scope.categories = [];
	
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
	
	$scope.loadCategory = function() {
        $scope.categories = [];

        $http({
            method: 'POST',
            url: myndie.apiURL + "category/list",
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }

            $scope.categories = data.message;
            
        });      
    }
	
	
	
	  
    
    $scope.bindEvents = function() {
        
        /**
        * Handle the event when the user submits the article details form.
        */
		$(".btn.btn-primary").click(function(e) {
			if($(this).find(">:first-child").hasClass("fa-save"))
			{
				$scope.save();
			}
			else
			{
				$scope.delete();
			}
		});
		
        $("#frmDetails").submit(function(e) {
            e.preventDefault();
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
	
	
	/**
    * Delete the article
    */
    $scope.delete = function() {
        
        utils.hideMessages();   // Hide all message divs

        $http({
            method: 'POST',
            url: myndie.apiURL + "article/delete",
            data: 'ids=' + $scope.id,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }   
            
            // return to the main listing screen
            $window.location.href = "#!/articles";
        });     
                    
    }

    // We're editing an existing article, load the article data
    if($scope.id > 0) {
        $scope.load();
    } else {
        // We're adding a new article. 
    }           
    
    $scope.loadCategory();   
    $scope.bindEvents();   
}); 
