app.controller('ArticleListCtrl', function ($scope, $http, $window, utils) {
    $scope.articles = [];    // Start off with empty clients array
    $scope.asignment_days = [];    // Start off with empty clients array
    $scope.pages = [];
	$scope.locations = [];
    $scope.selectedPageNo = false;
    $scope.selectedLocationOption = false;
    
    var self = this;
    
    $("#navArticles a").focus();
	
	$scope.load = function() {
		$scope.loadNotAsignmentArticle();
		$scope.loadAsignmentArticle();
	}

    $scope.loadNotAsignmentArticle = function() {
		
		if(!$scope.selectedLocationOption)
			return;
	
        var form = $("#frmFilters");
        var params = $(form).serialize();
		params += "&is_not_allocated=1";
		params += "&location=" + $scope.selectedLocationOption.id;
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
	
	$scope.loadLocations = function() {
        $scope.locations = [];

        $http({
            method: 'POST',
            url: myndie.apiURL + "location/list",
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }

            $scope.locations = data.message;
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
            
            $scope.loadNotAsignmentArticle();
        });     
    }
	
	
	$scope.loadAsignmentArticle = function(dateObj) {
		if(!$scope.selectedLocationOption)
			return;
			
        var form = $("#frmFilters");
        var params = $(form).serialize();
		params += "&is_not_allocated=0";
		params += "&location=" + $scope.selectedLocationOption.id;
		
		
		if(dateObj === undefined)
			dateObj = new Date();

		var month = dateObj.getMonth() + 1;
		if(month < 10) month = '0' + month;
		var day = dateObj.getDate();
		var year = dateObj.getFullYear();
		params += "&published_date=" + day + "/" + month + "/" + year;
		
        // Clear the clients array
        $scope.asignment_days = [];

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
            
			$('table#asignment_days tr td:nth-child(2)').empty();
			$('table#asignment_days tr td:nth-child(2)').append('<a href="#!articles/detail/0">Add</a>');
            $scope.asignment_days = data.message;
			for (var i in $scope.asignment_days)
			{
				$('table#asignment_days tr#position_' + $scope.asignment_days[i].position_no +' td:nth-child(2)').empty();
				$('table#asignment_days tr#position_' + $scope.asignment_days[i].position_no +' td:nth-child(2)').append('<a href="#!articles/detail/' + $scope.asignment_days[i].id +'">' + $scope.asignment_days[i].title + '</a>');
			}
            
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
        $scope.loadNotAsignmentArticle();        
    }   
	
	
	/**
    * Save the article
    * If the article ID is 0, a new article will be created, otherwise we update the current article
    */
    $scope.save = function(article) {
        
        utils.hideMessages();   // Hide all message divs
		
        // Save the article
        var url =  myndie.apiURL + "article/save/" + article.id;

        $http.post(url, article).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            utils.showSuccess("The article was updated successfully");
        });            
                    
    }
	
	$scope.dropSuccessHandler = function($event,index){
		// Remove article on client side
		$( $event.currentTarget ).remove();
	};
	$scope.onDrop = function($event,$data){
		//Save article (server side)
		dateObj = $('#published_date').datepicker('getDate');
			
		var month = dateObj.getMonth() + 1;
		if(month < 10) month = '0' + month;
		var day = dateObj.getDate();
		var year = dateObj.getFullYear();
		$data.is_not_allocated = 0;
		$data.published_date = day + "/" + month + "/" + year;
		$data.position_no = $( $event.currentTarget ).children("td:first-child").text();
		$scope.save($data);
	
		// Add article on client side
		var element =  $( $event.currentTarget ).children("td:nth-child(2)");
		element.empty();
		element.append('<a href="#!articles/detail/' + $data.id +'">' + $data.title + '</a>');
	};
	
	$scope.bindEvents = function() {
        
        $('#published_date').datepicker({
			todayHighlight: 'true',
		}).on('changeDate', function(e){
			$scope.loadAsignmentArticle($('#published_date').datepicker('getDate'));
		});
		
		$('#published_date').datepicker('setDate', new Date());
    }
	
	$scope.bindEvents();  
    
	$scope.loadLocations();
	$scope.load();
}); 
