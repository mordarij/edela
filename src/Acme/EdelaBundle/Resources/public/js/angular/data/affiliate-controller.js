edelaControllers.controller('AffiliateLinksController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.mainClass = "inner-page";

        $scope.loading = false;

        $scope.links = [];

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {caption: 'Партнерство'},
            {active: true, href: '#affiliate', caption: 'Ссылки'},
            {active: false, href: '#affiliate/withdraw', caption: 'Вывод средств'},
            {active: false, href: '#affiliate/rules', caption: 'Правила'},
        ]);

        $scope.addPromo = function (promo) {
            $scope.promo = promo;
            $scope.addLink();
        }

        $scope.addLink = function () {
            $scope.links.push({title: Math.floor(Math.random() * 5000)});
        }
    }]);

edelaControllers.controller('AffiliateWithdrawController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.mainClass = "inner-page";

        $scope.loading = false;

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {caption: 'Партнерство'},
            {active: false, href: '#affiliate', caption: 'Ссылки'},
            {active: true, href: '#affiliate/withdraw', caption: 'Вывод средств'},
            {active: false, href: '#affiliate/rules', caption: 'Правила'},
        ]);

    }]);

edelaControllers.controller('AffiliateRulesController', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {

        $scope.mainClass = "inner-page";

        $scope.loading = false;

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {caption: 'Партнерство'},
            {active: false, href: '#affiliate', caption: 'Ссылки'},
            {active: false, href: '#affiliate/withdraw', caption: 'Вывод средств'},
            {active: true, href: '#affiliate/rules', caption: 'Правила'},
        ]);

    }]);