app.controller('DashboardCtrl', function ($scope, $http, utils) {
    $scope.statistics_articles = false;
	$scope.SHOW_DAYS = 5;
	$scope.SHOW_DAYS_IN_MONTH = 30;
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
			
			var curDate = new Date();
			var first_show_day = new Date();//Should be the 1st monday of this week * will modify later
			var month = curDate.getMonth() + 1;
			if(month < 10) month = '0' + month;
			var day = curDate.getDate();
			var year = curDate.getFullYear();
			$('#statistics_schedules thead tr').append('<th>'+day+'</th>');
			for(var j = 1; j < $scope.SHOW_DAYS_IN_MONTH; j++)
			{
				curDate.setDate(curDate.getDate()+1);
				
				month = curDate.getMonth() + 1;
				if(month < 10) month = '0' + month;
				day = curDate.getDate();
				year = curDate.getFullYear();
				
				$('#statistics_schedules thead tr').append('<th>'+day+'</th>');
			}
			
			
			//calculate
			var count_loc = 0;
			var statistics_schedules = data.message; 
			for(var k in statistics_schedules)
			{
				var curDate = first_show_day;
				$('#statistics_schedules tbody').append('<tr></tr>');
				$('#statistics_schedules tbody tr').append('<td>'+k+'</td>');//will push location's name here
				for(var j in statistics_schedules[k])
				{
					var date_from = new Date(statistics_schedules[k][j].date_from);
					var date_to = new Date(statistics_schedules[k][j].date_to);
					console.log(curDate);
					console.log(date_from);
					console.log(date_to);
					var timeDiff = Math.abs(date_from.getTime() - curDate.getTime());
					var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
					console.log(timeDiff);
					console.log(diffDays);
					if(timeDiff > 0)
						$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td colspan="' + diffDays +'"></td>');
					
					timeDiff = Math.abs(date_to.getTime() - date_from.getTime());
					diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
					console.log(timeDiff);
					console.log(diffDays);
					if(timeDiff > 0)
						$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td colspan="' + diffDays +'"></td>');
					curDate = date_to;
				}
				count_loc++;
			}
        });      
    }
    
    $scope.bindEvents = function() {  
    }

	$scope.loadLocations();
    // $scope.loadStatisticsArticles();     
    
    $scope.bindEvents();   
}); 
