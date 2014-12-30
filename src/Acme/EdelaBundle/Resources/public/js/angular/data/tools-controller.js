edelaControllers.controller('ToolsController', ['$scope', '$http', '$rootScope', '$filter', 'toolsManager', function ($scope, $http, $rootScope, $filter, toolsManager) {
    $scope.loading = true;

    toolsManager.loadTools().then(function (tools) {
        $scope.tools = tools;
        $scope.loading = false;
    });

    $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
        {caption: 'Все приложения'},
        { active: true, href: '#tools', caption: 'Мои приложения' },
        { active: false, href: '#tools/about', caption: 'Что такое "Инструменты"?' },
    ]);

}]);

edelaControllers.controller('ToolsAboutController', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
    $scope.loading = true;

    $http.get('api/help/static/tools').success(function (data) {
        $scope.currentTopic = data;
        $scope.loading = false;
    });


    $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
        {caption: 'Все приложения'},
        { active: false, href: '#tools', caption: 'Мои приложения' },
        { active: true, href: '#tools/about', caption: 'Что такое "Инструменты"?' },
    ]);

}]);