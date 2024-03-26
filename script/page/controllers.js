const STATUS_OK         = "OK";
const RESPONSE_ARRAY    = 'RESPONSE';


var topbannerController = angular.module('topbannerController', ['ngAnimate']);


topbannerController.controller('TopbannerController', ['$scope', '$http', function($scope, $http) {
    
    $http.post('user/PublicUser.php?GET_USER_LIST=1').success(function(data){
        $scope.userList = (data); console.info("User list ready!");
    });
    console.info("AngularJS: Top banner controller ready!");
}]);