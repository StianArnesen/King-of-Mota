mainApp.controller('FollowupController', ['$scope', '$http', function($scope, $http){

        $http.post('utils/databaseReader.php?get_followup_list=1').success(function(data){
            $scope.followups = (data);
        });


    }]);