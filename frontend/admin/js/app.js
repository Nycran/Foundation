       
    var app = angular.module('myndieApp', ['SharedServices', 'ngRoute', 'ngDragDrop']);        
    
	
	app.config([
	  '$provide', function($provide) {
		return $provide.decorator('$rootScope', [
		  '$delegate', function($delegate) {
			$delegate.safeApply = function(fn) {
			  var phase = $delegate.$$phase;
			  if (phase === "$apply" || phase === "$digest") {
				if (fn && typeof fn === 'function') {
				  fn();
				}
			  } else {
				$delegate.$apply(fn);
			  }
			};
			return $delegate;
		  }
		]);
	  }
	]);
    /*******************
    * Angular App Routing
    */
    app.config(function($routeProvider, $locationProvider) {
        $routeProvider
        
            // route for the dashboard page
            .when('/dashboard', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/dashboard.html',
                controller  : 'DashboardCtrl'
            })   
            
            // route for the locations page
            .when('/locations', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/locations.html',
                controller  : 'LocationListCtrl'
            })  
            
            // route for the location details page
            .when('/locations/detail/:id', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/location.html',
                controller  : 'LocationCtrl'
            })   
            
            // route for the categories page
            .when('/categories', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/categories.html',
                controller  : 'CategoryListCtrl'
            })  
            
            // route for the category details page
            .when('/categories/detail/:id', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/category.html',
                controller  : 'CategoryCtrl'
            })  
			
			// route for the articles page
            .when('/articles', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/articles.html',
                controller  : 'ArticleListCtrl'
            })  

			// route for the article details page
            .when('/articles/detail/:id', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/article.html',
                controller  : 'ArticleCtrl'
            })  
            
            // route for the users page
            .when('/users', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/users.html',
                controller  : 'UserListCtrl'
            })  
            
            // route for the user details page
            .when('/users/detail/:id', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/user.html',
                controller  : 'UserCtrl'
            }) 
            
            // route for the sponsors listing page
            .when('/sponsors', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/sponsors.html',
                controller  : 'SponsorListCtrl'
            })  
            
            // route for the sponsor details page
            .when('/sponsors/detail/:id', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/sponsor.html',
                controller  : 'SponsorCtrl'
            })            
                                                            

            // route for the home page
            .when('/', {
                templateUrl : myndie.baseURL + '/frontend/admin/templates/partials/dashboard.html',
                controller  : 'DashboardCtrl'
            })
            
            // route for the logout function
            .when('/logout', {
                templateUrl : myndie.baseURL + 'frontend/admin/templates/partials/logout.html',
                controller  : 'LogoutCtrl'
            })             
            
            .otherwise('/', {
                templateUrl : myndie.baseURL + '/frontend/admin/templates/partials/dashboard.html',
                controller  : 'DashboardCtrl'
            })
            
        $locationProvider.hashPrefix('!');            
    }); 

    /*******************
    * Angular Directives
    */    
    
    // Define the EDIT button directive.
    app.directive("ncEditButton", function () {
       return {
          template: '<a data-id="{{item.id}}" href="javascript:void(0);" class="edit">Edit</a>',
          restrict: 'A',
          scope: {
             item: '=item',
             callbackFn: '&'
          },
          link: function(scope, element, attrs) {
             $(element).find("a").on("click", function () {
                //var itemID = $(this).attr("data-id");
                scope.callbackFn({arg1: scope.item});
             });
          }
       };
    });  
        
    // Renders edit and remove buttons
    // Note requires that the relevant item is assigned in the html
    app.directive("mynEditRemove", function ($window) {
        return {
            template: '<a href="javascript:;" class="btn btn-sm btn-success" data-action="edit" data-id="{{item.id}}"><i class="fa fa-edit"></i> Edit</a> &nbsp;' +
            '<a href="javascript:;" class=" btn btn-sm btn-danger" data-action="delete" data-id="{{item.id}}"><i class="fa fa-trash-o"></i> Remove</a>',
            restrict: 'A',
            scope: {
                item: '=item',
                callbackFn: '&'
            },
            link: function(scope, element, attrs) {
                $(element).find("a").on("click", function () {
                    var action = $(this).attr("data-action");
                    var id = $(this).attr("data-id");
                    scope.callbackFn({arg1: action, arg2: id});
                });
            }
        };
    });
    
    
    /**
    * Directive: mynIsoToUkDateTimeConverter 
    * Converts an ISO date time string into a UK formatted date time string
    */
    app.directive('mynIsoToUkDateTimeConverter', function () {
        // We use a WATCH directive for this as we need to wait
        // for data binding to finish before we try and detect and manipulate the date.
        return function (scope, element, attrs) {
            scope.$watch(attrs.myBackgroundImage, function(v) {
                var isoDate = $(element).html().trim();
                if(isoDate == "") {
                    return;
                }
                
                
                isoDate = isoDate.split(/[-: ]/);
                var objDate = new Date(isoDate[0], isoDate[1]-1, isoDate[2], isoDate[3], isoDate[4], isoDate[5]);                
  
                // Determine the meridian and convert from 24 hour to 12 hour
                var meridian = "AM";
                var hours = objDate.getHours();
                
                if(hours >= 12) {
                    meridian = "PM";
                    hours = hours - 12;
                }
                
                // Build the resulting UK date
                var ukDate = objDate.getDate() + "/" + (objDate.getMonth() + 1) + "/" + objDate.getFullYear() + " " + hours + ":" + objDate.getMinutes() + " " + meridian;
                
                // Write the date result into the element
                $(element).html(ukDate);
            });
        }
    });
    
    // Renders paging button
    // Note requires that the relevant item is assigned in the html
    app.directive("mynPager", function ($window, $timeout) {
        return {
            template: '<ul class="pagination">'  +
                '<li><a href="javascript:void(0);" class="previous"><i class="fa fa-angle-double-left"></i></a></li>' +
                '<li ng-repeat="page in pages" myn-pager-item page="page"></li>' +
                '<li><a href="javascript:void(0);" class="next"><i class="fa fa-angle-double-right"></i></a></li>',
            restrict: 'A',
            scope: {
                pages: '=pages',
                callbackFn: '&'
            },
            link: function(scope, element, attrs) {
                // After a small delay, set focus on the first page number.
                $timeout(function() {
                    var first_page = $(element).find("li:nth-child(2)").find("a");
                    $(first_page).focus();
                    $(first_page).addClass("active");
                }, 100);
                
                // Handle the event when the user clicks on the next or previous links.
                // note, the actual page numbers will not be captured by this method as they 
                // are handled in the child directive (i.e. at the time of binding the children do not yet exist)
                $(element).find("li").find("a").click(function(e) {
                    var selectedIndex = $(element).find("li a.active").parent().index();
                    
                    // Did the user click on the previous paging button
                    if($(this).hasClass("previous")) {
                        // If the user clicked on the previous button but we are currently on the first option
                        // then there's nothing to do.  Just ensure the focus state is returned to the first page
                        if(selectedIndex <= 1) {
                            $(element).find("li:nth-child(" + (selectedIndex + 1) + ")").find("a").focus();            
                        } else {
                            // Click on the next page number
                            $(element).find("li:nth-child(" + selectedIndex + ")").find("a").click();        
                        }
                    } else {  // The user clicked on the NEXT paging button
                        var maxIndex = $(element).find("li").size() - 2;
                                    
                        // If the user clicked on the next button but we are currently on the last option
                        // then there's nothing to do.  Just ensure the focus state is returned to the last page                                                                     
                        if(selectedIndex >= maxIndex) {
                            $(element).find("li:nth-child(" + (selectedIndex + 1) + ")").find("a").focus();
                        } else { 
                            // Click on the next paging button                
                            var newIndex = selectedIndex + 2;
                            $(element).find("li:nth-child(" + newIndex + ")").find("a").click();                                            
                        }
                        
                    }
                });
            },
            controller: function($scope) {
                this.handleClick = function(pageNo) {
                    $scope.callbackFn({arg1: pageNo})
                }
            }
        };
    });    
    
    // Renders paging button
    app.directive("mynPagerItem", function ($timeout) {
        return {
            template: '<a href="javascript:void(0);" data-page="{{page.pageNo}}">{{page.pageNo}}</a>',
            restrict: 'A',
            scope: {
                page: '=page',
                callbackFn: '&'
            },
            require: '^mynPager',
            link: function(scope, element, attrs, parentCtrl) {  
                $(element).find("a").on("click", function () {
                    var pageNo = $(this).attr("data-page");
                    $(this).parent().parent().find("li a").removeClass("active");
                    $(this).addClass("active");
                    $(this).focus();
                    parentCtrl.handleClick(pageNo); 
                });
            }
        };
    });    
    
    /**
    * Define a global variables service
    * so we can share data between controllers
    */
    app.service('globals', function ($http) {
        var roles = false;  // Roles will be populated with an associative array once the roles have been lazy loaded

        return {
            getRoles: function (callbackMethod) {
                if(!roles) {

                    $http({
                        method: 'GET',
                        url: myndie.apiURL + "role/list"
                    }).success(function (data) {
                        if(!data.status) {
                            alert(data.message);
                            return;
                        }
                        
                        // Get the user from the data
                        roles = data.message; 
                        
                        callbackMethod(roles);
                    });                           
                } else {
                    callbackMethod(roles);
                }
            },
            setProperty: function(value) {
                roles = value;
            }
        };
    });    
    
    app.service('utils', function () {
        var property = 'First';

        return {
            getObjectSize: function (obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                
                return size;  
            },
            
            showSuccess: function(message) {
                $("#sucessMessage span").html(message);
                $("#sucessMessage").removeClass("hidden");                
            },
            
            showError: function(message) {
                $("#errorMessage span").html(message);
                $("#errorMessage").removeClass("hidden");                
            },            
            
            hideMessages: function() {
                $("#errorMessage").addClass("hidden");
                $("#sucessMessage").addClass("hidden");
            },
            
            convertISOToUKDate : function(d) {
                if((d == null) || (d == "")) {
                    return d;
                }
                
                var elements = d.split("-");
                if(elements.length != 3) {
                    return "";
                }
                
                var result = elements[2] + "/" + elements[1] + "/" + elements[0];
                return result;
            }            
        };
    }); 
   
         