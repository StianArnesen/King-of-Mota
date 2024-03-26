var mainApp = angular.module('mainApp', [
    'ngRoute',
    'topbannerController'
]);


mainApp.config(['$routeProvider',  function($routeProvider){
    $routeProvider.
    when('/customers', {
        templateUrl:    'page/view/customer-view.html',
        controller:     'CustomerListController'
    }).otherwise({
        controller:     'topbannerController'
    });

}]);