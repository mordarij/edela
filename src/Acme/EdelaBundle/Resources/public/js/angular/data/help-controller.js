edelaControllers.controller('HelpListController', ['$scope', '$http', '$rootScope',
    '$filter', '$routeParams', '$route',
    function ($scope, $http, $rootScope, $filter, $routeParams, $route) {

        $scope.loading = true;
        $http.get('api/help/list').success(function (data) {
            $scope.topics = data;
            $scope.loading = false;
            $("#hover").removeClass("active");
            $("#hover1").removeClass("active");
            //var currentId = !$routeParams.id ? data[0]['id'] : $routeParams.id;
            //$scope.changeTopic(currentId);
        });

      /*  var hot = [];
        var currentHot = 0;
        $http.get('api/help/hot').success(function(data){
            hot = data;
            $scope.hotQuestion = hot[currentHot];
        });

        $scope.nextHotQuestion = function(){
            if (currentHot < (hot.length - 1)){
                currentHot++;
            } else {
                currentHot = 0;
            }
            $scope.hotQuestion = hot[currentHot];
        };

        $scope.prevHotQuestion = function(){
            if (currentHot > 0){
                currentHot--;
            } else {
                currentHot = hot.length - 1 ;
            }
            $scope.hotQuestion = hot[currentHot];
        };

        $scope.changeTopic = function (id) {
            $http.get('api/help/' + id).success(function (data) {
                $scope.currentTopic = data;
                $scope.loading = false;

            })
        };

        var lastRoute = $route.current;
        $scope.$on('$locationChangeSuccess', function (event) {
            if ($route.current.$$route.controller === 'HelpListController') {
                // Will not load only if my view use the same controller
                $route.current = lastRoute;
            }
        });*/

    }]);

edelaControllers.controller('FaqListController', ['$scope', '$http', '$rootScope',
    '$filter', '$routeParams', '$route',
    function ($scope, $http, $rootScope, $filter, $routeParams, $route) {
        $scope.$parent.mainClass = 'inner-page';
        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {caption: 'Помощь'},
            {active: true,  caption: 'Вопросы'},
        ]);
    }]);

edelaControllers.controller('StaticLicenseController', ['$scope', '$http',
    function ($scope, $http) {
        $scope.loading = true;

        $http.get('api/help/static/license').success(function (data) {
            $scope.currentTopic = data;
            $scope.loading = false;
            $(".menu-center a").each(function(){
            	$(this).removeClass("active");
            })
        });

    }]);

edelaControllers.controller('StaticAboutController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.loading = true;
        $http.get('api/help/static/about').success(function (data) {
            $scope.currentTopic = data;
            $scope.loading = false;
        });

    }]);

