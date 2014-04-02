app.controller('DashboardCtrl', function ($scope, $http, $route, $routeParams, $window, $timeout, globals, utils) {
    $scope.statistics_articles = false;
    $scope.statistics_schedules = false;
	$scope.SHOW_DAYS = 5;
	$scope.SHOW_DAYS_IN_MONTH = 30;
	$scope.locations = [];
	
	$scope.sponsor = false;
	$scope.schedule = false;
	$scope.loc_i = false;
	$scope.schedule_i = false;
	$scope.selectedLocation = false;
    
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
			
			var end_day = new Date();
			var first_day = new Date();//Should be the 1st monday of this week * will modify later
			var month = end_day.getMonth() + 1;
			if(month < 10) month = '0' + month;
			var day = end_day.getDate();
			var year = end_day.getFullYear();
			$('#statistics_schedules thead tr').append('<th>'+day+'</th>');
			for(var j = 1; j < $scope.SHOW_DAYS_IN_MONTH; j++)
			{
				end_day.setDate(end_day.getDate()+1);
				
				month = end_day.getMonth() + 1;
				if(month < 10) month = '0' + month;
				day = end_day.getDate();
				year = end_day.getFullYear();
				
				$('#statistics_schedules thead tr').append('<th>'+day+'</th>');
			}
			
			
			//calculate
			var count_loc = 0;
			$scope.statistics_schedules = data.message; 
			var statistics_schedules = data.message; 
			for(var k in statistics_schedules)
			{
				var curDate = new Date(first_day);
				$('#statistics_schedules tbody').append('<tr></tr>');
				$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td>'+statistics_schedules[k][0].sharedLocation[0].name+'</td>');//will push location's name here
				for(var j in statistics_schedules[k])
				{
					
					var date_from = new Date(statistics_schedules[k][j].date_from);
					var date_to = new Date(statistics_schedules[k][j].date_to);
					if(date_from > end_day)
						continue;
					if(date_to > end_day)
						date_to = end_day;
					var timeDiff = Math.abs(date_from.getTime() - curDate.getTime());
					var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 

					if(timeDiff > 0)
						$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td class="danger" colspan="' + diffDays +'"></td>');
					
					timeDiff = Math.abs(date_to.getTime() - date_from.getTime());
					diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
					if(timeDiff > 0)
					{
						var _class = "";
						if(statistics_schedules[k][j].is_confirmed == "1")
							_class = "success";
						else
							_class = "warning";
						$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td data-sponsor-id="'+statistics_schedules[k][j].sharedSponsor[0].id+'" data-loc-i="'+k+'" data-schedule-i="'+j+'" class="' + _class + '" colspan="' + diffDays +'"></td>');
					}
					curDate = date_to;
					
				}
				
				if(end_day > curDate)
				{
					var timeDiff = Math.abs(end_day.getTime() - curDate.getTime());
					var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
					if(timeDiff > 0)
						$('#statistics_schedules tbody tr:nth-child('+(count_loc+1)+')').append('<td class="danger" colspan="' + diffDays +'"></td>');
				}
				count_loc++;
			}
			
			
			$scope.bindEvents();
        });      
    }
	
	$scope.loadSchedule = function() {
		
		
		
		setTimeout(function() {
            // Load the scheduleText epic editor
            $scope.loadEpicEditor("epiceditor3", "schedule_text", "");
            
            // Invoke the second epic editor for the notes
            $scope.loadEpicEditor("epiceditor4", "schedule_notes", "");        
		}, "500");
		
		$scope.schedule = $scope.statistics_schedules[$scope.loc_i][$scope.schedule_i];
		
		// Loop through the locations and find the selected location
		for(var l in $scope.locations) {
			if($scope.locations[l].id = $scope.schedule.sharedLocation[0].id) {
				$scope.selectedLocation = $scope.locations[l];
				break; 
			}    
		}
		
		$scope.$apply();
		
	}
	
	$scope.loadSponsor = function(id) {
            
		$http({
            method: 'GET',
            url: myndie.apiURL + "sponsor/get/" + id
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            // Get the sponsor from the data
            $scope.sponsor = data.message; 
            
            if($scope.sponsor.sharedImage) {
                var image = $scope.sponsor.sharedImage[0];
                var image_path = myndie.baseURL + image.path;
                
                $("#logoImageWrapper").html('<img src="' + image_path + '_thumb.jpg?=' + Math.floor(Math.random() * 99999) + '" width="150" />');
                $("#deleteLogoWrapper").show();
                $("#upload_logo").hide();
            }
            
            $scope.$apply();
			
			// The modal window is opened by bootstap, and we have to wait for the modal
			// to open fully before calling epic editor.
			$scope.waitModalOpen("#scheduleModal", function() {

				var default_text = $("#default_text").val();
				$scope.loadEpicEditor("epiceditor", "default_text", default_text);
				
			}, 0);  
			
            

            // Setup QQ uploader
            // Setup hero image uploader
            var gUploader = new qq.FileUploader(
            {
                // pass the dom node (ex. $(selector)[0] for jQuery users)
                element: document.getElementById('upload_logo'),
                // path to server-side upload script
                action: myndie.apiURL + 'images/upload/sponsor_logo',
                params: {"id" : id},    
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                sizeLimit: 2100000, // max size 
                onComplete: function(id, fileName, data)
                {
                    if(data.status)
                    {
                        // The upload completed successfully.
                        // Load the image into the wrapper
                        $("#logoImageWrapper").html('<img src="' + myndie.baseURL + data.image_path + '_thumb.jpg?=' + Math.floor(Math.random() * 99999) + '" width="150" />');
                        
                        // Show the remove button
                        $("#deleteLogoWrapper").show();
                        
                        // Hide the upload button
                        $("#upload_logo").hide();
                    }
                }
            });            
            
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
	
	/**
    * The waitModalOpen function checks if a bootstrap modal window is open or not
    * If it is NOT open, it waits for 200 millisecs and then tries again.   The loop
    * will run at most for 10 times before timing out.
    * 
    */
    $scope.waitModalOpen = function(selector, callback, attemptNo) {

        if(!$(selector).hasClass("in")) {
            attemptNo++;
            
            if(attemptNo < 10) {
                setTimeout(function() {
                    $scope.waitModalOpen(selector, callback, attemptNo);                
                }, "500");
            } else {
                alert("waitModelOpen - Timeout error");
            }
        } else {
            callback();
        }
    }
	
	$scope.showScheduleDetails = function(spondor_id)
	{
		$scope.loadSponsor(spondor_id);
		$scope.loadSchedule();
		$("#scheduleModal").modal("show");
	}
	
	/**
    * Save the sponsor
    * If the sponsor ID is 0, a new sponsor will be created, otherwise we update the current sponsor
    */
    $scope.saveSponsor = function() {
        
        utils.hideMessages();   // Hide all message divs

        // Because the default text textarea is written to automagically by epiceditor,
        // Angular is NOT aware of the changes to the value.
        // Explicity set the default text of the sponsor object in angular.
        var default_text = $("#default_text").val();        
        $scope.sponsor.default_text = default_text;     
        
        // Save the sponsor
        var url =  myndie.apiURL + "sponsor/save/" + $scope.sponsor.id;

        $http.post(url, $scope.sponsor).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new sponsor, return to the main listing screen
            // if($scope.id == 0) {
                // $window.location.href = "#!/sponsors/detail/" + data.message;
                // return;
            // }
            
            utils.showSuccess("The sponsor was updated successfully");
        });            
                    
    }
    
    $scope.bindEvents = function() {  
		$('#statistics_schedules tbody td').click(function(e){
			var sponsor_id = $(this).attr('data-sponsor-id');
			if(sponsor_id == undefined)
				sponsor_id = 0;
			$window.location.href = "#!/sponsors/detail/" + sponsor_id;
			
			// $scope.loc_i = $(this).attr('data-loc-i');
			// $scope.schedule_i = $(this).attr('data-schedule-i');
			// $scope.showScheduleDetails(sponsor_id);
		});
		
		/**
        * Handle the event when the user submits the sponsor details form.
        */
        $("#frmSponsorDetails").submit(function(e) {
            e.preventDefault();

            $scope.saveSponsor();
        }); 
    }

	$scope.loadLocations(); 
    
    // $scope.bindEvents();   
}); 
