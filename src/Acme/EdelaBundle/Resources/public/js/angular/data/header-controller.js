edelaControllers.controller('HeaderController', [ '$scope', '$http', '$interval', '$location', '$rootScope', 'calendar', '$routeParams', 'currentUser', '$route', '$window',
    function ($scope, $http, $interval, $location, $rootScope, calendar, $routeParams, currentUser, $route, $window) {
        var updateHeaderData = function () {

            currentUser.updateProfile().then(function () {
                if (typeof $scope.profile != 'undefined' && currentUser.getProfile().exp != $scope.profile.exp) {
                    $scope.addExp(currentUser.getProfile().exp - $scope.profile.exp);
                    currentUser.getProfile().exp = $scope.profile.exp;
                }
                if (typeof $scope.profile != 'undefined' && currentUser.getProfile().bill != $scope.profile.bill) {
                    $scope.addBill(currentUser.getProfile().bill - $scope.profile.bill);
                    currentUser.getProfile().bill = $scope.profile.bill;
                }
                $scope.profile = currentUser.getProfile();
            });
        };

        $scope.calendar = calendar;
        $scope.calendar.addChangeListener(function () {
            angular.element('#main-calendar').datepicker("setDate", $scope.calendar.date);            
        });
        $scope.$watch('calendar.date', function () {
            $scope.calendar.triggerChange();
        });


        updateHeaderData();
        $interval(updateHeaderData, 5000);

        $scope.menuClass = function (page) {
            var current = $location.path().substring(1);
            return page === current ? "active" : "";
        };
        $scope.menu = {show: false, notifications: false, search: false
        };

        $scope.gotoPayment = function (balance) {
            balance = Number(balance);
//            if (balance < 10){
//                return;
//            }
            $location.path("/payment/recharge/" + balance);
        }

        $scope.breadcrumbsEnabled = false;

        $rootScope.$on('$locationChangeSuccess', function () {
            $scope.breadcrumbsEnabled = false;
            $scope.$parent.mainClass = '';
            $scope.menu.show = false;
            $scope.menu.notifications = false;
            if ($route.current.$$route.controller !== 'ActionsListController') {
                calendar.enabled = false;
            } else {
                calendar.enabled = true;
            }


        });

        $rootScope.$on('BREADCRUMBS_CHANGED', function (response, data) {
            if (data.length) {
                $scope.breadcrumbsEnabled = true;
                $scope.breadcrumbs = data;
            } else {
                $scope.breadcrumbsEnabled = false;
            }
            initHeights();
        });

        $scope.addExp = function (value) {
            $scope.profile.expChange = value;

            var span = $('<span>').text((value > 0 ? '+' : '') + value).appendTo($('.user-menu .like'));
            var expSpan = $('.user-menu .like span:first');

            expSpan.animate({'margin-top': -19}, 500, function () {
                $scope.profile.exp = $scope.profile.exp + value;
                $scope.$apply();
                expSpan.css('margin-top', 'auto').insertAfter(span);
                span.delay(1000).animate({'margin-top': -19}, 500, function () {
                    span.remove();
                });
            });
        }


        $scope.addBill = function (value) {
            $scope.profile.billChange = value;

            var span = $('<span>').text((value > 0 ? '+' : '') + value).appendTo($('.user-menu .bill'));
            var billSpan = $('.user-menu .bill span:first');

            billSpan.animate({'margin-top': -19}, 500, function () {
                $scope.profile.bill = $scope.profile.bill + value;
                $scope.$apply();
                billSpan.css('margin-top', 'auto').insertAfter(span);
                span.delay(1000).animate({'margin-top': -19}, 500, function () {
                    span.remove();
                });
            });
        }


        $scope.goto = function (link) {
            $location.path(link);
        }

        $scope.dayoff = {
            add: function () {
                var scope = this;
                $http.post('api/profile/dayoff', { date: calendar.date.format('yyyy-mm-dd') }).success(function (data) {
                    if (data.success) {
                        if (data.reason == 'created') {
                            $scope.profile.available_dayoffs--;
                        }
                        //TODO: mark dayoff
                        return;
                    } else if (data.reason == 'no-dayoffs') {
                        scope.popup = true;
                    }
                });
            },
            popup: false,
            options: [
                { num: 3, amount: 100 },
                { num: 5, amount: 150 },
                { num: 10, amount: 200 }
            ],
            select: { num: 3, amount: 100 },
            recharge: function(){
                $location.path('/payment/recharge/' + (this.select.amount - $scope.profile.bill));
                this.popup = false;
                calendar.extended = false;
            }
        }


        $scope.post = function (link) {
            $http.post(link);
        }

        $scope.notificationRead = function (notification, action, $event) {

            if (!action && (typeof notification.actions.default === 'undefined')) {
                return
            }
            $http({
                method: 'PATCH',
                url: 'api/notifications/' + notification.id,
                data: { is_read: true }
            });
            if (notification.actions) {
                if (action) {
                    eval(action);
                } else if (!action && notification.actions.default) {
                    eval(notification.actions.default);
                }
            }
            $event.stopPropagation();
        }

    }]);