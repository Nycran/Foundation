app.controller('ArticleCtrl', function ($scope, $http, $route, $routeParams, $window, $timeout, globals, utils) {
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
			
			$scope.$apply();
			
			var content = $("#content").val();
            $scope.loadEpicEditor("epiceditor2", "content", content);
			
			var notes = $("#notes").val();
            $scope.loadEpicEditor("epiceditor", "notes", notes);
			
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
	
	$scope.loadEpicEditor = function(container, textarea, defaultText) {

        var opts = {
            container: container,
            textarea: textarea,
            basePath: 'epiceditor',
            clientSideStorage: false,
            localStorageName: container,
            useNativeFullscreen: true,
            parser: marked,
            file: {
                name: 'epiceditor',
                defaultContent: defaultText,
                autoSave: 100
            },
            theme: {
                base: myndie.baseURL + 'frontend/admin/css/epiceditor/themes/base/epiceditor.css',
                preview: myndie.baseURL + 'frontend/admin/css/epiceditor/themes/preview/preview-dark.css',
                editor: myndie.baseURL + 'frontend/admin/css/epiceditor/themes/editor/epic-dark.css'
            },
            button: {
                preview: true,
                fullscreen: true,
                bar: "auto"
            },
            focusOnLoad: false,
            shortcut: {
                modifier: 18,
                fullscreen: 70,
                preview: 80
            },
            string: {
                togglePreview: 'Toggle Preview Mode',
                toggleEdit: 'Toggle Edit Mode',
                toggleFullscreen: 'Enter Fullscreen'
            },
            autogrow: false
        }
            
        // Invoke the first epic editor for the ad text
        var editor = new EpicEditor(opts).load();        
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
		// Because the default text textarea is written to automagically by epiceditor,
        // Angular is NOT aware of the changes to the value.
        var notes = $("#notes").val();        
        $scope.article.notes = notes;  
		var content = $("#content").val();       
        $scope.article.content = content;  
		
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
