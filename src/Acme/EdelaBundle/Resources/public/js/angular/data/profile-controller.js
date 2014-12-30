edelaControllers.controller('ProfileController', ['$scope', '$http', '$rootScope', 'currentUser', 'usersManager', '$fileUploader', '$location', function ($scope, $http, $rootScope, currentUser, usersManager, $fileUploader, $location) {
    $scope.mainClass = 'inner-page';

    $scope.click = function (item) {
        item.active = !item.active;
        $http({
            method: 'patch',
            url: 'api/profile/settings',
            data: { key: item.key, enabled: item.active }
        });
        setTimeout(function () {
            $('.lk-holder').trigger("ss-rearrange");
        }, 300);
    }

    var uploader = $scope.uploader = $fileUploader.create({
        scope: $scope,
        url: 'api/profile/photo',
        autoUpload: true
    });

    uploader.bind('complete', function (event, xhr, item, response) {
        $scope.data.profile.photo = '/' + response.webPath;
    });


    $scope.data = {
        subscribe: false,
        profile: false
    };

    $scope.password = { old: '', new_one: '', new_two: '' }
    $scope.invite = {};
    $scope.sendInvite = function () {
        usersManager.sendInvite($scope.invite);
        $scope.invite.popup = false;
    };
    $scope.showPopup = function () {
        $scope.invite.popup = true;
        $('.pop-up-add-friend').css("top", Math.max(0, (($(window).height() - $('.pop-up-add-friend').outerHeight()) / 4) +
            $(window).scrollTop()) + "px");
    }

    $scope.user = currentUser;

    $scope.profile = currentUser.getProfile();

    $scope.recharge = function (amount) {
        $location.path('payment/recharge/' + amount);
    }

    $scope.languages = [
        { id: 'ru', title: 'Русский' },
        { id: 'en', title: 'Английский' },
    ];

    $scope.timezones = [
        { id: 0, title: 'Лондон' },
        { id: 4, title: 'Москва' },
        { id: 6, title: 'Екатеринбург' },
        { id: 7, title: 'Новосибирск' },
        { id: 8, title: 'Красноярск' },
        { id: 9, title: 'Иркутск' },
        { id: 11, title: 'Владивосток' },
        { id: 12, title: 'Магадан' }
    ]
    $http.get('api/profile').success(function (data) {
        $scope.data = data;
        console.log(!data.settings.lk_privacy_disabled);
        $scope.items = [
            {caption: 'Личный кабинет'},
            { active: !data.settings.lk_user_disabled, click: $scope.click, caption: 'Пользователь', key: 'user' },
            { active: !data.settings.lk_privacy_disabled, click: $scope.click, caption: 'Приватность', key: 'privacy' },
            { active: !data.settings.lk_balance_disabled, click: $scope.click, caption: 'Баланс', key: 'balance' },
            { active: !data.settings.lk_additional_disabled, click: $scope.click, caption: 'Дополнительно', key: 'additional' }
        ];

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', $scope.items);


        setTimeout(function () {
            $('.lk-holder').trigger("ss-rearrange");
        }, 300);
    });

    $scope.saveProfileData = function () {
        currentUser.saveProfile($scope.data.profile);
    }
    $scope.changePassword = function () {
        $scope.password.old_error = false;
        if ($scope.password.new_one != $scope.password.new_two) {
            $scope.password.new_error = true;
            return;
        }
        $scope.password.new_error = false;
        currentUser.changePassword($scope.password).success(function (data) {
            if (data.success) {
                $scope.password = { old: '', new_one: '', new_two: '' };
            } else {
                if (data.message == 'old') {
                    $scope.password.old_error = true;
                }
                if (data.message == 'new') {
                    $scope.password.new_error = true;
                }
            }
        });
    }

}]);

edelaControllers.controller('ProfileLevelsController', ['$scope', '$http', '$rootScope', 'currentUser', 'globalVars', '$filter', function ($scope, $http, $rootScope, currentUser, globalVars, $filter) {
    $scope.mainClass = 'inner-page';
    $scope.loading = true;

    $scope.user = currentUser;
    $scope.profile = currentUser.getProfile();
    $scope.levels = globalVars.getVars().levels;
    $http.get('api/tools').success(function (tools) {
        for (var i in $scope.levels) {
            if (!$scope.levels.hasOwnProperty(i)) continue;
            $scope.levels[i].tools = $filter('filter')(tools, function (tool) {
                return tool.min_level <= $scope.levels[i].number;
            });
        }
        $scope.loading = false;
    });

    $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
        {caption: 'Уровни опыта'},
        { active: true, href: '#profile', caption: 'Ссылки' },
        { active: false, href: '#profile', caption: 'Вывод средств' },
        { active: false, href: '#profile', caption: 'Правила' },
    ]);

}]);

edelaControllers.controller('ProfilePaymentHistoryController', ['$scope', '$http', '$rootScope', 'currentUser', 'globalVars', '$filter', function ($scope, $http, $rootScope, currentUser, globalVars, $filter) {

    $scope.items = [
        {caption: 'Личный кабинет'},
        { active: true, href: '#profile', caption: 'История оплаты' }
    ];
    $scope.transactions = [];
    $http.get('api/payment/history').success(function (data) {
        $scope.transactions = data;
        $('.payment-history-holder').trigger("ss-rearrange");
    });

    $rootScope.$broadcast('BREADCRUMBS_CHANGED', $scope.items);

}]);

edelaControllers.controller('ProfilePaymentController', ['$scope', '$http', '$routeParams', '$rootScope', '$window', function ($scope, $http, $routeParams, $rootScope, $window) {

    $scope.items = [
        {caption: 'Личный кабинет'},
        { active: true, href: '#profile', caption: 'Оплата' }
    ];

    $scope.payment = {};
    switch ($routeParams.type) {
        case 'recharge':
            $scope.payment = { title: 'Пополнение баланса', amount: $routeParams.sum }
            break;
        default:
    }
    $scope.mainClass = 'inner-page';
    $scope.pay = function (method) {
        $http.get('api/payment/robokassagen?type=' + $routeParams.type + '&sum=' + $scope.payment.amount).success(function (data) {
            var location = 'http://auth.robokassa.ru/Merchant/Index.aspx?' +
                'MrchLogin=' + data.login + '&' +
                'OutSum=' + data.sum + '&' +
                'InvId=' + data.invId + '&' +
                'Desc=' + data.desc + '&' +
                'SignatureValue=' + data.sign + '&' +
                'IncCurrLabel=' + method;
            console.log(location);
            $window.location = location;
        })
    }

    $scope.yaData = { };
    $scope.yaPay = function (method) {
        method = 'AC';
        $http.get('api/payment/yagen?type=' + method + '&sum=' + $scope.payment.amount).success(function (data) {
            $scope.yaData.label = data.label;
            $scope.yaData.receiver = data.receiver;
            $scope.yaData.method = data.method;
            $scope.yaData.success_url = 'http://beta.e-dela.com/pay/yasuccess/' + data.id;
            setTimeout(function () {
                angular.element('#yandex-form').submit()
            }, 200);
        });
    }

    $rootScope.$broadcast('BREADCRUMBS_CHANGED', $scope.items);

}]);

edelaControllers.controller('ProfilePaymentResultController', ['$scope', '$http', '$routeParams', '$rootScope', '$window', function ($scope, $http, $routeParams, $rootScope, $window) {

    console.log(123);
    $scope.items = [
        {caption: 'Личный кабинет'},
        { active: true, href: '#profile', caption: 'Оплата' }
    ];

    $scope.mainClass = 'inner-page';
    $scope.loading = true;
    $scope.result = $routeParams.result;

    console.log($scope.result);
    $http.get('api/payment/details/' + $routeParams.id).success(function (data) {
        $scope.loading = false;
        switch (data.type) {
            default:
                data.title = 'Пополнение баланса';
                break;
        }
        $scope.payment = data;
    });

}]);