var Login = function() {
    var self = this;
    
    this.init = function() {
        this.bindEvents();
    }
    
    this.bindEvents = function() {
        // Handle the event when the user clicks on the login form submit button
        $("#loginForm").submit(function(e) {
            e.preventDefault();
            
            // Get the login params
            var params = $(this).serialize();
            
            // Submit the request to the API
            $.post(myndie.apiURL + "user/login", params, function(data) {
                // If the login failed, show the error message
                if(!data.status) {
                    $("#loginFailure").removeClass("hidden");
                    return;
                }
                
                // Login was successful
                // Hide the error message and redirect to dashboard.
                $("#loginFailure").addClass("hidden");
                
                window.location = myndie.baseURL + "admin/";
            }, "json");
        });    
    }
    
    this.init();
}

var objLogin = false;

$(document).ready(function() {
    objLogin = new Login();
})