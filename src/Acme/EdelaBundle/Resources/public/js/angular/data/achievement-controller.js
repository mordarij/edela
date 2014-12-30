edelaControllers.controller('AchievementsListController', ['$scope', '$http', '$location', '$filter', function ($scope, $http, $location, $filter) {

    $scope.loading = true;
    $http.get('api/profile/achievements').success(function(data){
        $scope.doneAchievements = [];
        var doneData = $filter('filter')(data, {progress: 100});
        var chunk = 5;
        for(var i = 0; i < doneData.length; i+=chunk){
            $scope.doneAchievements.push(doneData.slice(i, i+chunk));
        }
        $scope.achievements = data;
        $scope.loading = false;
    });

    $scope.notDone = function(achievement){
        return !achievement.progress || achievement.progress < 100;
    }

}]);