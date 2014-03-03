app.controller('DashboardCtrl', function ($scope, $http) {
    $scope.clients = [];    // Start off with empty clients array
    $scope.formData = {};
    $scope.editing = false;
    var self = this;
    
    $("#navDashboard a").focus();
    
    $scope.loadClients = function() {
        var form = $("#frmFilters");
        var params = $(form).serialize();
        
        // Clear the clients array
        $scope.clients = [];
        
        $http({
            method: 'POST',
            url: $(form).attr("action"),
            data: params,
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        }).success(function (data) {
            $scope.clients = data;    
        });            
    }
    
    $scope.clearForm = function() {
        $scope.frmDetails.$setPristine();
        $scope.formData = {};            
        $scope.$apply();           
    }
    
    $scope.save = function() {

        var action = base_url + "clients/save/";
        // If we are adding a new item, append 0 to the URL as the client ID as the API expects this
        if(!$scope.editing) {
            action += "0";
        } else {    // We are saving an existing client, add the client id to the URL.
            action += $scope.editing.id;
        }
        
        var params = this.formData;
        params["status"] = "active";           

        $http.post(action, params).success(function(data) {
            if(!data.status) {
                alert(data.message);
                return;
            }
            
            // If we are adding a new client, add the item to our items array
            if(!$scope.editing) {                
                // Clear the form and push the data to the clients array at the same time
                var item = {};
                item["id"] = data.message; // The ID of the new client is in the JSON response message field.
                
                for(var i in $scope.formData) {
                    item[i] = $scope.formData[i]; 
                }
                
                $scope.clients.push(item);
                
                $scope.editing = false; 
            }
            
            $scope.showList();              
        });             
    };  
    
    $scope.showDetail = function() {
        $("#clientList").addClass("hidden"); 
        $("#clientDetail").removeClass("hidden");                 
    }
    
    $scope.showList = function() {
        $("#clientDetail").addClass("hidden");                 
        $("#clientList").removeClass("hidden"); 
    }        
    
    $scope.editClient = function(client) {
        $scope.clearForm();
        $scope.editing = client; 
        $scope.formData = client;
        $scope.$apply();         
        
        $scope.showDetail();
    }   
    
    $scope.handleListAction = function(action) {
        switch(action) {
            case "add":
                $scope.editing = false;
                $scope.clearForm(); 
                $scope.showDetail();  
                break;
            
            default:
                alert("Unhandled action: " + action);
                break;              
        }
    }           
    
    // Handle the event when the user selects a new status
    $("#frmFilters select").change(function(){
        $scope.loadClients(); 
    });
    

    
    // Handle the event when the user clicks the Cancel button from the client details page
    $("#btnCancel").on("click", function() {
        $scope.showList(); 
    });
    
}); 
