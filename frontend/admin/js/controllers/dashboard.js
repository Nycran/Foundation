app.controller('DashboardCtrl', function ($scope, $http, utils) {
    $scope.statistics_articles = false;
	$scope.SHOW_DAYS = 5;
	$scope.locations = [];
    
    $("#navDashboard a").focus();
    
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
			
			$scope.loadStatisticsArticles();
			$scope.loadStatisticsSchedules();
        });      
    }
    
	$scope.loadStatisticsArticles = function() {
	
		var params = "is_not_allocated=0";
		var curDate = new Date();
		var month = curDate.getMonth() + 1;
		if(month < 10) month = '0' + month;
		var day = curDate.getDate();
		var year = curDate.getFullYear();
		params += "&published_date_ge=" + day + "/" + month + "/" + year;
	
        $http({
            method: 'POST',
			data : params,
            url: myndie.apiURL + "dashboard/getstatisticsarticles",
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
			
			var curDate = new Date();
			var month = curDate.getMonth() + 1;
			if(month < 10) month = '0' + month;
			var day = curDate.getDate();
			var year = curDate.getFullYear();
			var arr_show_day = [];
			arr_show_day.push(day + "/" + month + "/" + year);
			$('#statistics_articles thead tr th:nth-child(2)').html(day + "/" + month + "/" + year);
			for(var j = 1; j < $scope.SHOW_DAYS; j++)
			{
				curDate.setDate(curDate.getDate()+1);
				
				month = curDate.getMonth() + 1;
				if(month < 10) month = '0' + month;
				day = curDate.getDate();
				year = curDate.getFullYear();
				arr_show_day.push(day + "/" + month + "/" + year);
				
				$('#statistics_articles thead tr th:nth-child(' + (j + 2) + ')').html(day + "/" + month + "/" + year);
			}
            
			
			var temp = data.message; 
			$scope.statistics_articles = [];
			var i = 0;
			for(var k in $scope.locations)
			{
				var statistics_loc = [];
				statistics_loc.location = $scope.locations[k].name;
				statistics_loc.show_day = [];
				for(var j in arr_show_day)
				{
					if(temp[i] != undefined)
					{
						var published_date = utils.convertISOToUKDate(temp[i].published_date);
						if(published_date == arr_show_day[j] && temp[i].location == $scope.locations[k].id)
						{
							statistics_loc.show_day.push(temp[i].total);
							i++;
						}
						else
						{
							statistics_loc.show_day.push(0);
						}
					}
					else
					{
						statistics_loc.show_day.push(0);
					}
				}
				$scope.statistics_articles.push(statistics_loc);
			}
        });      
    }
	
	
	$scope.loadStatisticsSchedules = function() {
	
		var params = "is_not_allocated=0";
		var curDate = new Date();
		var month = curDate.getMonth() + 1;
		if(month < 10) month = '0' + month;
		var day = curDate.getDate();
		var year = curDate.getFullYear();
		params += "&date_from_ge=" + day + "/" + month + "/" + year;
	
        $http({
            method: 'POST',
			data : params,
            url: myndie.apiURL + "dashboard/getstatisticsschedules",
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
        });      
    }
    
    $scope.bindEvents = function() {  
    }

	$scope.loadLocations();
    // $scope.loadStatisticsArticles();     
    
    $scope.bindEvents();   
}); 
