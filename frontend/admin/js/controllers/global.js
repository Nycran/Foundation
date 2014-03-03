app.controller('GlobalCtrl', function ($scope, $http, $window) {
    var self = this;
    
    /**
    * Watch for a change in the jump to menu
    */
    $(document).on("change", "#jumpTo", function() {
        var new_url = $(this).val();
 
        if(new_url != "") {
            $window.location.href = new_url;
            
            $(this).val("");
        }
    });     
}); 
