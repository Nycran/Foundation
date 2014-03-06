app.controller('ArticleListCtrl', function ($scope, $http, $window) {
    $scope.articles = [];    // Start off with empty clients array
    $scope.pages = [];
    $scope.selectedPageNo = false;
    
    var self = this;
    
    $("#navArticles a").focus();

    $scope.load = function() {
        var form = $("#frmFilters");
        var params = $(form).serialize();
		params += "&is_allocated=0"
        // Clear the clients array
        $scope.articles = [];

        $http({
            method: 'POST',
            url: myndie.apiURL + $(form).attr("action"),
            data: params,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            $scope.articles = data.message;
            
            // Setup pagination.
            // If there is only 1 page, hide the paging area
            if((data.pages == undefined) || (data.pages <= 1)) {
                $("#paginationWrapper").addClass("hidden");
            } else {
            
                if(data.pages != $scope.pages.length) {
                    // Reset the pagination array.
                    $scope.pages = [];
                    for(p = 0; p < data.pages; p++) {
                        $scope.pages.push({pageNo: p+1});
                    }
                    
                    $("#paginationWrapper").removeClass("hidden");
                }
            } 
        });            
    }
    
    $scope.handleAction = function(action, id) {
        switch(action) {
          
            case "edit":
                $window.location.href = "#!/articles/detail/" + id;
                break;
                
            case "delete":
                if(confirm("Are you sure you wish to delete this article?")) {
                    $scope.doDelete(id);  
                }
                break;                
                
            default:
                alert("Unhandled action: " + action);
                break;              
        }
    }  
    
    $scope.doDelete = function(user_id) {
        $("#frmDelete #delete_ids").val(user_id);
        var params = $("#frmDelete").serialize();

        $http({
            method: 'POST',
            url: myndie.apiURL + "article/delete",
            data: params,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }   
            
            $scope.load();
        });     
    }
    
    /**
    * Handle the event when the user clicks on a page number
    * This event will be invoked by the mynPager directive.
    */
    $scope.handlePageClick = function(pageNo) {
        // Set the page number in the form
        $("#page").val(pageNo);
        
        // Reload the listing
        $scope.load();        
    }   
	
	$scope.bindEvents = function() {
        
        $('.datepicker').datepicker().on('changeDate', function(e){
			$scope.load();
		});;
    }
	
	$scope.bindEvents();  
    
    $scope.load();
}); 
