app.controller('SponsorCtrl', function ($scope, $http, $route, $routeParams, $window, $timeout, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.sponsor = false;
    $scope.scheduleID = 0;
    $scope.schedule = false;
    $scope.schedules = [];
    $scope.locations = [];
    $scope.pages = [];
    $scope.selectedLocation = false;
    
    $("#navSponsors a").focus();
    $("#deleteLogoWrapper").hide();
 
    
    // If the sponsor ID was passed in the URL, grab it.
    if($routeParams.id != undefined) {
        $scope.id = $routeParams.id;
    }
    
    $scope.load = function() {
        $http({
            method: 'GET',
            url: myndie.apiURL + "sponsor/get/" + $scope.id
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
            var default_text = $("#default_text").val();
            $scope.loadEpicEditor("epiceditor", "default_text", default_text);

            // Setup QQ uploader
            // Setup hero image uploader
            var gUploader = new qq.FileUploader(
            {
                // pass the dom node (ex. $(selector)[0] for jQuery users)
                element: document.getElementById('upload_logo'),
                // path to server-side upload script
                action: myndie.apiURL + 'images/upload/sponsor_logo',
                params: {"id" : $scope.id},    
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
    
    $scope.bindEvents = function() {
        /**
        * Handle the event when the user submits the sponsor details form.
        */
        $("#frmDetails").submit(function(e) {
            e.preventDefault();

            $scope.save();
        });        
    }
    
    /**
    * Handle the event when a user clicks on the Remove button
    * to delete the sponsor logo
    */
    $scope.deleteImage = function() {
        var params = {};
        params["id"] = $scope.id;
        
        var url =  myndie.apiURL + "sponsor/delete_logo/" + $scope.id;

        $http.post(url, params).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // Clear the sponsor logo from the wrapper
            $("#logoImageWrapper").html("");
            
            // Hide the remove button
            $("#deleteLogoWrapper").hide();
            
            // Show the upload button
            $("#upload_logo").show();
            
            
            utils.showSuccess("The sponsor logo was removed successfully");
        });        
    }
    
    /**
    * Save the sponsor
    * If the sponsor ID is 0, a new sponsor will be created, otherwise we update the current sponsor
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs

        // Because the default text textarea is written to automagically by epiceditor,
        // Angular is NOT aware of the changes to the value.
        // Explicity set the default text of the sponsor object in angular.
        var default_text = $("#default_text").val();        
        $scope.sponsor.default_text = default_text;     
        
        // Save the sponsor
        var url =  myndie.apiURL + "sponsor/save/" + $scope.id;

        $http.post(url, $scope.sponsor).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // If we just added a new sponsor, return to the main listing screen
            if($scope.id == 0) {
                $window.location.href = "#!/sponsors/detail/" + data.message;
                return;
            }
            
            utils.showSuccess("The sponsor was updated successfully");
        });            
                    
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
    
    $scope.showScheduleWindow = function() {
        
        $scope.selectedLocation = false;    // Clear selected location
        $scope.scheduleID = 0;
        
        // Initialise a blank schedule object
        $scope.schedule = {
            sponsor_id : $scope.id,
            use_default_text : 0,
            location_id : 0,
            date_form : "",    
            date_to : "",
            text : "",
            notes : ""
        };
        
        // The modal window is opened by bootstap, and we have to wait for the modal
        // to open fully before calling epic editor.
        $scope.waitModalOpen("#scheduleModal", function() {

            // Load the scheduleText epic editor
            $scope.loadEpicEditor("epiceditor3", "schedule_text", "");
            
            // Invoke the second epic editor for the notes
            $scope.loadEpicEditor("epiceditor4", "schedule_notes", "");
            
        }, 0);      
    }
    
    $scope.loadSchedules = function() {
        var params = {};
        
        var form = $("#frmScheduleList");
        var params = $(form).serialize();

        // Clear the schedules array
        $scope.schedules = [];

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
            
            $scope.schedules = data.message;
            
            // Loop through the schedule results and populate the location name.
            // RedBean loads the related locations into the sharedLocation array.
            for(var s in $scope.schedules) {
                var thisSchedule = $scope.schedules[s];
                thisSchedule.location_name = thisSchedule.sharedLocation[0].name;
                thisSchedule.location_id = thisSchedule.sharedLocation[0].id;
                
                // Convert ISO dates to UK date.
                thisSchedule.date_from = utils.convertISOToUKDate(thisSchedule.date_from);
                thisSchedule.date_to = utils.convertISOToUKDate(thisSchedule.date_to);                
            }
            
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
    
    /**
    * Handles the event when the user wants to save a schedule
    */
    $scope.saveSchedule = function(e) {
        
        // Get the location ID from the selected lcoation.
        $scope.schedule.location_id = $scope.selectedLocation.id;

        if(!$("#schedule_use_default_text").is(":checked")) {
            $scope.schedule.use_default_text = 0;
        }
        
        // Because the text and notes textareas are written to automagically by epiceditor,
        // Angular is NOT aware of the changes to the values.
        // Explicity set the default text of the sponsor object in angular.
        $scope.schedule.text = $("#schedule_text").val();                  
        $scope.schedule.notes = $("#schedule_notes").val();
        
        var url =  myndie.apiURL + "schedule/save/" + $scope.scheduleID;

        $http.post(url, $scope.schedule).success(function(data) {        
            if(!data.status) {
                utils.showError(data.message);
                return;
            }
            
            // Close the modal window
            $('#scheduleModal').modal('hide');

            // Reload the schedule listing.
            $scope.loadSchedules();
        });            
    }
    
    $scope.doDelete = function(schedule_id) {

        $("#frmDelete #delete_ids").val(schedule_id);
        var params = $("#frmDelete").serialize();

        $http({
            method: 'POST',
            url: myndie.apiURL + "schedule/delete",
            data: params,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            if(!data.status) {
                alert(data.message);
                return;
            }   
            
            $scope.loadSchedules();
        });     
    }    
    
    $scope.handleAction = function(action, id) {
        switch(action) {
          
            case "edit":
                // Tell bootstrap to show the modal window
                $("#scheduleModal").modal("show");
                
                // Initialise the modal
                $scope.showScheduleWindow();
                
                // Find the schedule item that the user selected in the list.
                for(var s in $scope.schedules) {
                    var thisSchedule = $scope.schedules[s];
                    if(thisSchedule.id == id) {
                        // Set the scheduleID and schedule in the scope.
                        $scope.scheduleID = id;
                        $scope.schedule = thisSchedule;
                        
                        // Inject the sponsor ID into the schedule item
                        $scope.schedule.sponsor_id = $scope.id;
                        
                        // Loop through the locations and find the selected location
                        for(var l in $scope.locations) {
                            if($scope.locations[l].id = $scope.schedule.location_id) {
                                $scope.selectedLocation = $scope.locations[l];
                                break; 
                            }    
                        }
                        
                        $scope.$apply();
                        break;
                    }
                }                
                
                break;
                
            case "delete":
                if(confirm("Are you sure you wish to delete this scheduled item?")) {
                    $scope.doDelete(id);  
                }
                break;                
                
            default:
                alert("Unhandled action: " + action);
                break;              
        }
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
    
    $scope.setDefaultText = function() {
        /*
        if($("#schedule_use_default_text").is(":checked")) {
            if($scope.sponsor.default_text != "") {
                $scope.loadEpicEditor("epiceditor3", "schedule_text", $scope.sponsor.default_text);
            }
        }
        */
    }
    
    // Load the list of locations
    $http.post(myndie.apiURL + "location/list", {}).success(function(data) {        
        if(!data.status) {
            utils.showError(data.message);
            return;
        }
        
        $scope.locations = data.message;           
        
        // We're editing an existing sponsor, load the sponsor data
        if($scope.id > 0) {
            $scope.load();
        } else {
            // We're adding a new sponsor.
            // Hide anyting that should be hidden for new users
            $(".hideOnNew").addClass("hidden");         
        }              
    });        
    
    $scope.bindEvents();   

    // Setup the datepickers
    $('.datepicker').datepicker({
        format : "dd/mm/yyyy",
        autoclose : true
    });
 }); 
