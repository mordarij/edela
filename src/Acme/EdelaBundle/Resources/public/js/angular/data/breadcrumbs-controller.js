edelaControllers.controller('BreadcrumbsController', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
        $rootScope.$on('BREADCRUMBS_UPDATE', function(response, data){
            $scope.action = data;
        });
}]);