edelaControllers.controller('ActionsListController', [
    '$scope', '$http', '$rootScope', '$filter', 'actionsManager', 'tasksManager', 'globalVars', 'calendar', '$routeParams', '$route', '$location', 'toolsManager', '$controller',
    function ($scope, $http, $rootScope, $filter, actionsManager, tasksManager, globalVars, calendar, $routeParams, $route, $location, toolsManager, $controller) {
        $scope.vars = globalVars.getVars();
        $scope.melodies = [1];
        $scope.loading = true;
        if ($routeParams.date && !isNaN(new Date($routeParams.date))) {
            calendar.setDate($routeParams.date);
        }
        var actionsLoaded = false;
        var toolsLoaded = false;
        var shapeshiftLoaded = false;
        var updateShape = function () {
            if (actionsLoaded && toolsLoaded) {
                setTimeout(function () {
                    $('.planned-business').shapeshift({
                        selector: '.box',
                        handle: '.btn-move',
                        enableDrag: true,
                        paddingX: 0,
                        paddingY: 0,
                        minColumns: 4,
                        align: "left",
                        animated: false,
                        dragWhitelist: '.dragbox',
                        colWidth: 235,
                        gutterX: 10,
                        cutoffEnd: $scope.actions.length + $scope.tools.length + 1,
                        cutoffStart: $scope.tools.length
                    });
                }, 10);                
            }
        }
        $('body').on('ss-rearranged', '.planned-business', function (e, box) {
            angular.element(box).scope().action.setPosition($(box).index());
        });

        function updateActions() {
            $scope.loading = true;
            actionsManager.loadActions(null, calendar.date).then(function (actions) {
                $scope.actions = actions;
                $scope.loading = false;
                actionsLoaded = true;
                updateShape();
                $("#hover").addClass("active");
            });            
        }

        updateActions();
        function updateComments() {
            $http.get('/actions/comments').success(function (data) {
                $scope.comments = data;
            });
        }

//        updateComments();

        calendar.addChangeListener(function () {
            $location.path('/' + calendar.date.format('yyyy-mm-dd'))
            updateActions();
        });

        toolsManager.loadTools(true).then(function (tools) {
            $scope.tools = tools;
            toolsLoaded = true;
            updateShape();
        });

        var lastRoute = $route.current;
        $scope.$on('$locationChangeSuccess', function (event) {
            if ($route.current.$$route.controller === 'ActionsListController') {
                // Will not load only if my view use the same controller
                $route.current = lastRoute;
            }
        });


        $scope.refreshTasks = function () {
            tasksManager.loadTasks().then(function (tasks) {
                $scope.tasks = tasks;
            });

            $http.get('api/tasks/done').success(function (data) {
                for (var i in data) {
                    data[i].date = new Date(data[i].done_at).format('d mmmm');
                }
                $scope.doneTasks = data;
            });
        }

   //     $scope.refreshTasks();

        $scope.goal = 0;

        $scope.executeTask = function (task) {
            $http({
                method: 'POST',
                url: 'api/tasks/' + task.id + '/execute'
            }).success(function (data) {
                if (data.success) {
                    task.done = data.done;
                }
            });
        }

        $scope.progressed = function (action) {
            return Boolean(action.progress);
        }

        $scope.executeSubaction = function (subaction, action) {

//            if (action.progress) {
//                return false;
//            }
//
//            if (subaction.progress) {
//                return false;
//            }

            $http({
                method: 'POST',
                url: 'api/subactions/' + subaction.id + '/execute'
            }).success(function (data) {
                if (data.success) {
                    subaction.progress = true;
                    if ($filter('filter')(action.subactions, {progress: true}).length == action.subactions.length) {
                        action.done++;
                        action.progress = 1;
                    }
                }
            });
        };

        $scope.progress = {num: 0};
        var updateProgress = function () {
            $scope.progress.num = 0;
            var actions = $filter('filterDelayed')($scope.actions, false);
            var total = actions.length;
            for (var i in actions) {
                if (!actions.hasOwnProperty(i)) continue;
                var action = actions[i];
                var progress = 0;
                if (globalVars.getVars().action_types[action.action_type_id].tkey != 'done' || (action.subactions || []).length == 0) {
                    progress = Number((action.progress || 0) > 0);
                } else {
                    progress = $filter('filter')(action.subactions, {progress: true}).length / action.subactions.length
                }
                $scope.progress.num += 100 * progress / total;
            }
        }
        $scope.$watch('actions', updateProgress, true);

        var blankAction = {
            action_type_id: 1,
            done: 0,
            dynamic_type_id: 1,
            id: 11,
            is_private: false,
            periodicity: 127,
            periodicity_interval: 1,
            progress: 0,
            progressHour: null,
            progressMinute: null,
            progressSecond: null,
            repeat_amount: 40,
            start_at: "2014-07-31",
            start_time: "2014-07-31T00:00:00+0700",
        };
        $scope.newAction = angular.copy(blankAction);
        $scope.display = {showNew: false};

        $("body").on('keypress', 'textarea.new_action', function (e) {
            if (e.keyCode == 13) {
                $scope.addAction($(this).data('delayed'));
            }
        });

        $scope.addAction = function (isDelayed) {
        	if($scope.newAction.title && $scope.newAction.title!="" && $scope.newAction.title!="Название..."){
            if (isDelayed) {
                $scope.newAction.start_at = null;
            } else {
                $scope.newAction.start_at = new Date().toISOString().slice(0, 10);
            }

//            actionsManager.addAction($scope.newAction).then(function (action) {
//                $scope.actions.push(action);
//            });
            $scope.actions.push(actionsManager.addAction($scope.newAction));
            $scope.newAction = angular.copy(blankAction);
            $scope.display.showNew = false;
            setTimeout(updateShape, 10);
        	}else{
        		alert("Введите название ежедненвого дела");
        	}
        };

        $scope.gotoStats = function () {
            $location.path('statistics');
        }

        $scope.runDelayed = function (action) {
            action.start_time = new Date();
//            action.isDelayed = false;
        }

        var blankTask = {
            createdAt: "2014-07-16T14:48:45+0700",
            done: false,
            is_important: false,
            is_urgent: false,
            is_sms_notification: false,
            parent: 0
        };
        $scope.newTask = angular.copy(blankTask);

        $scope.addTask = function (parent, $event) {
            if (parent) {
                $scope.newTask.parent = parent.id;
            }
            tasksManager.addTask($scope.newTask).then(function (task) {
                $scope.tasks.push(task);
                $scope.newTask = angular.copy(blankTask);
                if (parent) {
                    task.edit = true;
                    setTimeout(function () {
                        $($event.target).parents('li:first').find('input').focus()
                    }, 200);
                }
            });
        }
        $scope.createSubtask = function (task) {
            task.createAndSave().then(function () {
                task.edit = false
            }, function () {
                var ind = $scope.tasks.indexOf(task);
                $scope.tasks.splice(ind, 1)
            });
        }
        $rootScope.$on('actions:pool:removed', function (response, action) {
            var index = $scope.actions.indexOf(action);
            if (index > -1) {
                $scope.actions.splice(index, 1);
            }
        }, true)
    }]);

edelaControllers.controller('ActionJointContriller', ['$scope', '$routeParams', 'actionsManager', function ($scope, $routeParams, actionsManager) {

    $scope.loading = true;

    $scope.progressed = function (action) {
        return Boolean(action.progress);
    }

    actionsManager.loadAction($routeParams.id).then(function (action) {
        action.getJointInfo().then(function (data) {
            $scope.users = data;
            $scope.loading = false;
        })
    });

}]);

edelaControllers.controller('ActionsEditController', ['$scope', '$http', '$rootScope', 'globalVars', 'actionsManager', 'goalsManager', 'currentUser',
    function ($scope, $http, $rootScope, globalVars, actionsManager, goalsManager, currentUser) {
        var beforeEdit;
        $scope.times = {};
        $scope.times.hours = ["00", "01", "02", "03"];
        $scope.times.minutes = ["00", "15", "30", "45"];
        $scope.cancelEdit = function () {
            $scope.action.setData(beforeEdit);
            $scope.action.subactions = beforeEdit.subactions;
            $scope.action.tags = beforeEdit.tags;
            $scope.showEditPopup = false;
        };
        $scope.save = function () {
            $scope.action.saveChanges();
            $scope.showEditPopup = false;
        }

        $scope.remove = function () {
            actionsManager.removeAction($scope.action);
            $scope.showEditPopup = false;              
            if($scope.actions){
            setTimeout(function () {
                $('.planned-business').shapeshift({
                    selector: '.box',
                    handle: '.btn-move',
                    enableDrag: true,
                    paddingX: 0,
                    paddingY: 0,
                    minColumns: 4,
                    align: "left",
                    animated: false,
                    dragWhitelist: '.dragbox',
                    colWidth: 235,
                    gutterX: 10,
                    cutoffEnd: $scope.actions.length + $scope.tools.length + 1,
                    cutoffStart: $scope.tools.length
                });
            }, 200);
            }
        }

        goalsManager.loadGoals().then(function (goals) {
            $scope.goals = goals;
        });

        $("body").on('keypress', 'input.new_subaction', function (e) {
            if (e.keyCode == 13) {
            	if($scope.newSubaction.title!="" && $scope.newSubaction.title!="Название"){
            		e.preventDefault();
            		$scope.addSubaction();
            	}
            }
        });
        $("body").on('keypress', 'input.new_tag', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $scope.action.addTag($scope.newTag.title)
                $scope.newTag.title = '';
            }
        });
        $scope.addSubaction = function () {
        		$scope.action.addSubaction($scope.newSubaction.title);
        		$scope.newSubaction.title = '';
        }

        $rootScope.$on('EDIT_ACTION', function (response, data) {
            $scope.settings = {tab: 'tab1'};
            $scope.vars = globalVars.getVars();
            $scope.action = actionsManager._retrieveInstance(data.jsId, data);
//        $scope.action = data;
            $scope.action.dynamic_type_id = Number(data.dynamic_type_id);
            $scope.action.action_type_id = Number(data.action_type_id);
            data.action_time = data.action_time || "00:00";
            data.action_time_start = data.action_time_start || "00:00";
            data.action_time_finish = data.action_time_finish || "00:00";
            data.notification_time = data.notification_time || "00:00";
            $scope.action.getJointInfo().then(function (data) {
                $scope.joint = [];
                for (var i in data) {
                    if (!data.hasOwnProperty(i)) continue;
                    if (data[i].user.id != currentUser.getProfile().id) {
                        $scope.joint.push(data[i]);
                    }
                }
            });

            if (typeof data.action_time.hour == 'undefined') {
                data.action_time_start = data.action_time_start || "00:00";
                data.action_time_finish = data.action_time_finish || "00:00";
                $scope.action.action_time = {
                    hour: data.action_time.split(':')[0],
                    minute: data.action_time.split(':')[1]
                }
                $scope.action.action_time_start = {
                    hour: data.action_time_start.split(':')[0],
                    minute: data.action_time_start.split(':')[1]
                }
                $scope.action.action_time_finish = {
                    hour: data.action_time_finish.split(':')[0],
                    minute: data.action_time_finish.split(':')[1]
                }
                $scope.action.notification_time = {
                    hour: data.notification_time.split(':')[0],
                    minute: data.notification_time.split(':')[1]
                }
            }
            beforeEdit = angular.copy($scope.action);
            $scope.showEditPopup = true;
            $scope.settingsBriefly = true;
        });
        $scope.newSubaction = {};
        $scope.newTag = {};
    }]);


edelaControllers.controller('TasksEditController', ['$scope', '$http', '$rootScope', 'globalVars', 'tasksManager', 'goalsManager', function ($scope, $http, $rootScope, globalVars, tasksManager, goalsManager) {
    var beforeEdit;

    function createRange(lowEnd, highEnd) {
        var start = lowEnd;
        var array = [start];
        while (start < highEnd) {
            start++;
            array.push(start);
        }
        return array;
    }

    $scope.times = {};
    $scope.times.hours = createRange(0, 23);
    $scope.times.minutes = createRange(0, 59);
    $scope.years = ["2014", "2015"];
    $scope.months = [
        {id: 1, title: 'Январь'},
        {id: 2, title: 'Февраль'},
        {id: 3, title: 'Март'},
        {id: 4, title: 'Апрель'},
        {id: 5, title: 'Май'},
        {id: 6, title: 'Июнь'},
        {id: 7, title: 'Июль'},
        {id: 8, title: 'Август'},
        {id: 9, title: 'Сентябрь'},
        {id: 10, title: 'Октябрь'},
        {id: 11, title: 'Ноябрь'},
        {id: 12, title: 'Декабрь'}
    ];
    $scope.getDaysInMonth = function (year, month) {
        function daylightSavingAdjust(date) {
            if (!date) {
                return null;
            }
            date.setHours(date.getHours() > 12 ? date.getHours() + 2 : 0);
            return date;
        }

        return createRange(1, 32 - daylightSavingAdjust(new Date(year, month - 1, 32)).getDate());
    };

    $scope.cancelEdit = function () {
        $scope.task.setData(beforeEdit);
//        $scope.task.tags = beforeEdit.tags;
        $scope.showEditPopup = false;
    };
    $scope.save = function () {
        $scope.task.createAndSave();
        $scope.showEditPopup = false;
    }

    goalsManager.loadGoals().then(function (goals) {
        $scope.goals = goals;
    });

    $rootScope.$on('EDIT_TASK', function (response, data) {
        $scope.vars = globalVars.getVars();
        $scope.task = tasksManager._retrieveInstance(data.id, data);

        var d = new Date();
        var curr_date = d.getDate();
        var curr_month = d.getMonth() + 1;
        var curr_year = d.getFullYear();

        data.date = data.date || curr_year + "-" + curr_month + "-" + curr_date;
        data.notification_time = data.notification_time || "00:00";
        if (typeof data.date.month == 'undefined') {
            $scope.task.date = {
                year: data.date.split('-')[0],
                month: Number(data.date.split('-')[1]),
                day: Number(data.date.split('-')[2])
            };

        }
        if (typeof data.notification_time.hour == 'undefined') {
            $scope.task.notification_time = {
                hour: Number(data.notification_time.split(':')[0]),
                minute: Number(data.notification_time.split(':')[1])
            }
        }

        beforeEdit = angular.copy($scope.task);
        $scope.showEditPopup = true;
    });
    $scope.newTag = {};
}]);


edelaControllers.controller('ActionsSamplesController', ['$scope', '$http', '$rootScope', '$routeParams', function ($scope, $http, $rootScope, $routeParams) {
    $scope.mainClass = 'inner-page';
    $scope.loading = true;

    $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
        {caption: 'Примеры ежедневных дел'},
        {caption: 'Примеры ежедневных дел', active: true},

    ]);

    $http.get('api/samples').success(function (data) {
        $scope.samples = data;
        $scope.categories = [];
        for (var i in data) {
            if (!data.hasOwnProperty(i)) continue;
            $scope.categories = $scope.categories.concat(data[i].categories);
        }
        $scope.categories = $.unique($scope.categories);
        $scope.filterCat = $scope.categories[0].title;
        $scope.loading = false;
    });

    $scope.addAction = function (action) {
        action.added = true;
        var goal = $routeParams.goal || 0;
        $http({
            url: 'api/samples/' + action.id,
            method: 'POST',
            data: {goal: goal}
        }).success(function (data) {
            if (data) {
                action.added = true;
            }
        });
    }

}]);