app.controller('ArticleCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.article = false;
	$scope.selectedCategoryOption = false;  // Will be set to the selected category option
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
			// $scope.selectedCategoryOption = $scope.article
			$scope.$apply();
			$scope.updateStatusAllocated();
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
			if($scope.id > 0)
			{
				for (var i in $scope.categories)
				{
					if($scope.categories[i].id == $scope.article.category)
					{
						$scope.selectedCategoryOption = $scope.categories[i];
						break;
					}
				}
			}
        });      
    }
	
	
	
	  
    
    $scope.bindEvents = function() {
		
        $("#frmDetails").submit(function(e) {
            e.preventDefault();
        });  

		$("#is_not_allocated").change(function(e) {
            $scope.updateStatusAllocated();
        });  

		$('#published_date').datepicker({
			autoclose: 'true'
		});
    }
	
	$scope.updateStatusAllocated = function() {
		if($("#is_not_allocated").is(":checked")) {
			$('input[name="position_no"]').attr("disabled", "disabled");
			$('#published_date').attr("disabled", "disabled");
		}
		else
		{
			$('input[name="position_no"]').removeAttr("disabled");
			$('#published_date').removeAttr("disabled");
		}
	}
    
    /**
    * Save the article
    * If the article ID is 0, a new article will be created, otherwise we update the current article
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs
        
		if(!$("#is_not_allocated").is(":checked")) {
			$scope.article.is_not_allocated = 0;
		}
		
		
		$scope.article.category = $scope.selectedCategoryOption.id;
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
	$scope.updateStatusAllocated();
    $scope.bindEvents();   
}); 
