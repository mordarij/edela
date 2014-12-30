var edelaApp = angular.module('edelaApp', [
            'ngRoute',
            'edelaControllers',
            'angular-loading-bar',
            'angularFileUpload',
//            'angular-bootstrap-select',
//            'angular-bootstrap-select.extra',
            'nya.bootstrap.select',
            'ngSanitize',
            'd3',
            'ngQuickDate',
            'ngAudio'
        ])
        .directive('placeholder', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    var placeholder = attrs.placeholder;
                    if (placeholder) {
                        $(element).placeholder();
                    }
                }
            };
        })
        .directive('slider', function () {
            return {
                restrict: 'A',
                scope: {
                    sliderOptions: '='
                },
                link: function (scope, element, attrs) {
                    var div = angular.element(attrs.sliderDiv);
                    var optionsKeys = Object.keys(scope.sliderOptions);
                    for (var i in optionsKeys) {
                        if (!optionsKeys.hasOwnProperty(i)) continue;
                        optionsKeys[i] = Number(optionsKeys[i]);
                    }
                    $(element).slider({
                        min: Math.min.apply(Math, optionsKeys),
                        max: Math.max.apply(Math, optionsKeys),
                        range: "min",
                        slide: function (event, ui) {
                            div.html(scope.sliderOptions[ui.value]);
                        }
                    });
                }
            };
        })
        .filter('unsafe', function ($sce) {
            return function (val) {
                return $sce.trustAsHtml(val);
            };
        })
        .directive('scroll', ['$window', function ($window) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    angular.element($window).bind('scroll', function () {
                        if (this.pageYOffset > 200) {
                            angular.element('.scroll-up').show();
                        } else {
                            angular.element('.scroll-up').hide();
                        }
                    });
                }
            };
        }])
        .directive('phonenumber', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    $(element).intlTelInput({
                            defaultCountry: 'ru'
                        }
                    )
                    ;
                }
            }
                ;
        })
        .
        directive('shortcut', ['$window', '$rootScope', function ($window, $rootScope) {
            return {
                link: function (scope) {
                    angular.element($window).on('keypress', function (e) {
                        console.log(e.ctrlKey, e.keyCode);
                        if (e.ctrlKey && e.keyCode == 10) {
                            $rootScope.$emit('SEND_ERROR_PRESSED', e);
                        }
                    });
                }
            }
        }])
        .directive('scrollpane', function ($compile, $timeout) {
            return {
                link: function ($scope, element, attrs) {

                    element.jScrollPane({
                        autoReinitialise: true,
                        autoReinitialiseDelay: 100
                    });
                }
            }
        })
        .directive('fancybox', function ($compile, $timeout) {
            return {
                link: function ($scope, element, attrs) {
                    element.fancybox({
                        padding: 0,
                        autoScale: false,
                        transitionIn: 'none',
                        transitionOut: 'none',
                        overlayColor: '#000',
                        overlayOpacity: 0.6,
                        hideOnOverlayClick: false,
                        hideOnContentClick: false,
                        enableEscapeButton: false,
                        showNavArrows: false,
                        onComplete: function () {
                            $timeout(function () {
                                $compile($("#fancybox-content"))($scope);
                                $scope.$apply();
                                $.fancybox.resize();
                            })
                        }
                    });
                }
            }
        })
        .directive('datepicker', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
//                    $.datepicker.regional['ru'] = {
                    var opts = {
                        closeText: 'Закрыть',
                        prevText: '&#x3c;Пред',
                        nextText: 'След&#x3e;',
                        currentText: 'Сегодня',
                        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
                            'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
                        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
                        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                        weekHeader: 'Нед',
                        dateFormat: 'dd.mm.yy',
                        firstDay: 1,
                        isRTL: false,
                        showMonthAfterYear: false,
                        yearSuffix: ''};
                    $(element).datepicker(opts);
                }
            }
        })
        .directive('gridster', function(){
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    $(element).gridster({
                        widget_margins: [10, 10],
                        widget_base_dimensions: [235, 273],
                        widget_selector: "div.dragbox"
                    });
                }
            }
        })
        .directive('gridly', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    $(element).gridly({
                        base: 235,
                        columns: 4,
                        draggable: {
                            zIndex: 10000,
                            selector: '.dragbox'
                        }
                    });
                }
            }
        })
        .directive('shapeshift', function () {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    console.log(123);
                    var handler = attrs.shapeshift;
                    console.log(handler);
                    $(element).shapeshift({
                        handle: handler,
                        enableDrag: false,
                        paddingX: 0,
                        paddingY: 0,
                        animationSpeed: 600,
                        minColumns: 3,
                        align: "left",
                        animated: false
                    });
                }
            }
        })
    ;


edelaApp.config(['cfpLoadingBarProvider', function (cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeSpinner = false;
}]);

edelaApp.filter('sumByKey', function () {
    return function (data, key) {
        if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
            return 0;
        }

        var sum = 0;
        for (var i = data.length - 1; i >= 0; i--) {
            sum += parseInt(data[i][key]);
        }

        return sum;
    };
});

edelaApp.filter('filterDelayed', function () {
    return function (actions, isDelayed) {
        var filtered = [];
        angular.forEach(actions, function (item) {
            if (item.isDelayed() == isDelayed) {
                filtered.push(item);
            }
        });
        return filtered;
    };
});

edelaApp.filter('links', function () {
    return function (note) {
        if (typeof note === 'undefined') return;
        var replacePattern = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
        return note.replace(replacePattern, '<a href="$1" target="_blank">$1</a>');
    };
});

edelaApp.filter('capitalize', function () {
    return function (input, scope) {
        if (input != null)
            input = input.toLowerCase();
        return input.substring(0, 1).toUpperCase() + input.substring(1);
    }
});

edelaApp.service('globalVars', ['$http', function ($http) {
    var variables = {};

    var promise = $http.get('api/variables').success(function (data) {
        variables = data;
    });
    return {
        promise: promise,
        getVars: function () {
            return variables;
        }
    }
}]);

edelaApp.service('currentUser', ['$http', 'globalVars', '$filter', function ($http, globalVars, $filter) {
    var profile = {};
    var promise = $http.get('api/profile/short', {
        ignoreLoadingBar: true
    }).success(function (data) {
        profile = data;
    });
    return {
        promise: promise,
        getProfile: function () {
            return profile;
        },
        updateProfile: function () {
            return $http.get('api/profile/short', {
                ignoreLoadingBar: true
            }).success(function (data) {
                for (var i in data.notifications) {
                    if (!data.notifications.hasOwnProperty(i)) continue;
                    if (data.notifications[i].actions) {
                        data.notifications[i].actions = JSON.parse(data.notifications[i].actions);
                    }
                }
                profile = data;
            });
        },
        getLevel: function () {
            var levels = globalVars.getVars().levels;
            var level = levels[0];
            for (var i in levels) {
                if (!levels.hasOwnProperty(i)) continue;
                if (levels[i].start_exp <= profile.total_exp && levels[i].start_exp > level.start_exp) {
                    level = levels[i];
                }
            }
            return level;
        },
        getNextLevel: function () {
            var levels = globalVars.getVars().levels;
            var level = levels[levels.length - 1];
            for (var i in levels) {
                if (!levels.hasOwnProperty(i)) continue;
                if (levels[i].start_exp > profile.total_exp && levels[i].start_exp < level.start_exp) {
                    level = levels[i];
                }
            }
            return level;
        },
        saveProfile: function (data) {
            console.log(data.birthday);
            if (typeof data.birthday == 'object') {
                data.birthday = data.birthday.format('dd/mm/yyyy');
            } else if (typeof data.birthday !== 'undefined'){
                data.birthday = new Date(data.birthday).format('dd/mm/yyyy');
            }
            $http({
                method: 'PATCH',
                url: 'api/profile',
                data: { profile_edit: {
                    fullname: data.fullname,
                    email: data.email,
                    phone: data.phone,
                    birthday: data.birthday,
                    language: data.language,
                    timezone: data.timezone
                }}
            }).success(function () {

            });
        },
        changePassword: function (data) {
            return $http({
                method: 'PATCH',
                url: 'api/profile/password',
                data: data
            });
        }
    }
}]);

edelaApp.service('calendar', ['$rootScope', function ($rootScope) {
    var calendar = {
        date: new Date(),
        enabled: false,
        listeners: [],
        extended: false,
        seleckpicker: false,
        setDate: function (date) {
            this.date = new Date(date);
            this.triggerChange();
        },
        goToday: function () {
            this.date = new Date();
            this.triggerChange();
        },
        goYesterday: function () {
            this.date.setDate(this.date.getDate() - 1);
            this.triggerChange();
        },
        goTomorrow: function () {
            this.date.setDate(this.date.getDate() + 1);
            this.triggerChange();
        },
        addChangeListener: function (listener) {
            this.listeners.push(listener);
        },
        triggerChange: function () {
            for (var i in this.listeners) {
                if (!this.listeners.hasOwnProperty(i)) continue;
                this.listeners[i]();

            }
        }

    };
    return calendar;
}]);

edelaApp.factory('socket', function ($rootScope) {
    var socket = io.connect('http://' + window.location.host + ':8001/', {
        rememberTransport: false,
        transports: [
            'websocket',
            'flashsocket',
            'xhr-multipart',
            'xhr-polling',
            'polling'
        ]
    });

    return {
        on: function (eventName, callback) {
            socket.on(eventName, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                    callback.apply(socket, args);
                });
            });
        },
        emit: function (eventName, data, callback) {
            if (typeof data == 'function' && typeof callback == 'undefined') {
                callback = data;
                data = null;
            }
            socket.emit(eventName, data, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                    if (callback) {
                        callback.apply(socket, args);
                    }
                });
            });
        }
    };
});

edelaApp.directive('topcalendar', ['currentUser', function ($currentUser) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ctrl) {
            $(element).datepicker({
                closeText: 'Закрыть',
                prevText: '&#x3c;Пред',
                nextText: 'След&#x3e;',
                currentText: 'Сегодня',
                monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
                    'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
                dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
                dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                weekHeader: 'Нед',
                dateFormat: 'mm-dd-yy',
                firstDay: 1,
                showMonthAfterYear: false,
                yearSuffix: '',
                numberOfMonths: 2,
//                    altField: "#main-calendar-day",
//                    altFormat: "d MM <i>(DD)</i>",
                onSelect: function (date) {
                    ctrl.$setViewValue(new Date(date));
                    ctrl.$render();
                    scope.$apply();
                }
            });
        }
    };
}]);


edelaApp.config(['$routeProvider', '$locationProvider', '$provide',
    function ($routeProvider, $locationProvider, $provide) {
        var prefix = '';
        $routeProvider.
            when(prefix + '/achievements', {
                templateUrl: '/bundles/acmeedela/partials/achievements-list.html',
                controller: 'AchievementsListController'
            }).
            when(prefix + '/id' + currentUserId, {
                templateUrl: '/bundles/acmeedela/partials/actions-list.html',
                controller: 'ActionsListController',
                resolve: {
                    'globalVarsData': function (globalVars) {
                        return globalVars.promise;
                    }
                }
            }).
            when(prefix + '/display', {
                templateUrl: '/bundles/acmeedela/partials/achievements-list.html',
                controller: 'FriendsProfileController'
            }).
            when(prefix + '/friends/find', {
                templateUrl: '/bundles/acmeedela/partials/friends-find.html',
                controller: 'FriendsFindController'
            }).
            when(prefix + '/friends', {
                templateUrl: '/bundles/acmeedela/partials/friends-list.html',
                controller: 'FriendsListController'
            }).
            when(prefix + '/tools/about', {
                templateUrl: '/bundles/acmeedela/partials/static.html',
                controller: 'ToolsAboutController'
            }).
            when(prefix + '/tools', {
                templateUrl: '/bundles/acmeedela/partials/tools.html',
                controller: 'ToolsController'
            }).
            when(prefix + '/profile/levels', {
                templateUrl: '/bundles/acmeedela/partials/profile-levels.html',
                controller: 'ProfileLevelsController',
                resolve: {
                    'globalCurrentUserData': function (currentUser) {
                        return currentUser.promise;
                    },
                    'globalVarsData': function (globalVars) {
                        return globalVars.promise;
                    }
                }
            }).
            when(prefix + '/payment/result/:result/:id', {
                templateUrl: '/bundles/acmeedela/partials/payment_result.html',
                controller: 'ProfilePaymentResultController'
            }).
            when(prefix + '/payment/:type/:sum?', {
                templateUrl: '/bundles/acmeedela/partials/payment.html',
                controller: 'ProfilePaymentController',
                resolve: {
                    'globalCurrentUserData': function (currentUser) {
                        return currentUser.promise;
                    }
                }
            }).
            when(prefix + '/profile/payment/history', {
                templateUrl: '/bundles/acmeedela/partials/profile-payment-history.html',
                controller: 'ProfilePaymentHistoryController',
                resolve: {
                    'globalCurrentUserData': function (currentUser) {
                        return currentUser.promise;
                    }
                }
            }).
            when(prefix + '/profile', {
                templateUrl: '/bundles/acmeedela/partials/profile.html',
                controller: 'ProfileController',
                resolve: {
                    'globalCurrentUserData': function (currentUser) {
                        return currentUser.promise;
                    },
                    'globalVarsData': function (globalVars) {
                        return globalVars.promise;
                    }
                }
            }).
            when(prefix + '/goals/:id/edit', {
                templateUrl: '/bundles/acmeedela/partials/goals-edit.html',
                controller: 'GoalsEditController'
            }).
            when(prefix + '/goals/:id/actions', {
                templateUrl: '/bundles/acmeedela/partials/goal-actions-list.html',
                controller: 'GoalsActionsController',
                resolve: {
                    'globalVarsData': function (globalVars) {
                        return globalVars.promise;
                    }
                }
            }).
            when(prefix + '/actions/samples', {
                templateUrl: '/bundles/acmeedela/partials/actions-samples.html',
                controller: 'ActionsSamplesController'
            }).
            when(prefix + '/goals', {
                templateUrl: '/bundles/acmeedela/partials/goals-list.html',
                controller: 'GoalsListController'
            }).
            when(prefix + '/faq', {
                templateUrl: '/bundles/acmeedela/partials/faq-list.html',
                controller: 'FaqListController'
            }).
            when(prefix + '/license', {
                templateUrl: '/bundles/acmeedela/partials/static.html',
                controller: 'StaticLicenseController'
            }).
            when(prefix + '/about', {
                templateUrl: '/bundles/acmeedela/partials/static.html',
                controller: 'StaticAboutController'
            }).
            when(prefix + '/statistics', {
                templateUrl: '/bundles/acmeedela/partials/statistics-list.html',
                controller: 'StatisticsListController'
            }).
            when(prefix + '/help/:id?', {
                templateUrl: '/bundles/acmeedela/partials/help-list.html',
                controller: 'HelpListController'
            }).
            when(prefix + '/affiliate/withdraw', {
                templateUrl: '/bundles/acmeedela/partials/affiliate-withdraw.html',
                controller: 'AffiliateWithdrawController'
            }).
            when(prefix + '/affiliate/rules', {
                templateUrl: '/bundles/acmeedela/partials/affiliate-rules.html',
                controller: 'AffiliateRulesController'
            }).
            when(prefix + '/affiliate', {
                templateUrl: '/bundles/acmeedela/partials/affiliate-links.html',
                controller: 'AffiliateLinksController'
            }).
            when(prefix + '/actions/:id/joint', {
                templateUrl: '/bundles/acmeedela/partials/actions-joint.html',
                controller: 'ActionJointContriller'
            }).
            when(prefix + '/:date?', {
                templateUrl: '/bundles/acmeedela/partials/actions-list.html',
                controller: 'ActionsListController',
                resolve: {
                    'globalVarsData': function (globalVars) {
                        return globalVars.promise;
                    }
                }
            }).

            otherwise({
                redirectTo: prefix + '/'
            });


    }
]);
