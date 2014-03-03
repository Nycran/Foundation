app.controller('SponsorCtrl', function ($scope, $http, $route, $routeParams, $window, globals, utils) {
    $scope.id = 0;  // Default the ID to 0.
    $scope.sponsor = false;
    
    $("#navSponsors a").focus();
 
    
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
            
            $scope.doMarkdown();
        });      
    }
    
    $scope.doMarkdown = function() {
        
        var default_text = $("#default_text").val();

        var opts = {
            container: 'epiceditor',
            textarea: "default_text",
            basePath: 'epiceditor',
            clientSideStorage: true,
            localStorageName: 'epiceditor',
            useNativeFullscreen: true,
            parser: marked,
            file: {
                name: 'epiceditor',
                defaultContent: default_text,
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
            
        var editor = new EpicEditor(opts).load();     
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
    * Save the sponsor
    * If the sponsor ID is 0, a new sponsor will be created, otherwise we update the current sponsor
    */
    $scope.save = function() {
        
        utils.hideMessages();   // Hide all message divs
        
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

    // We're editing an existing sponsor, load the sponsor data
    if($scope.id > 0) {
        $scope.load();
    } else {
        // We're adding a new sponsor.
        // Hide anyting that should be hidden for new users
        $(".hideOnNew").addClass("hidden");         
    }           
    
    $scope.bindEvents();   
}); 
