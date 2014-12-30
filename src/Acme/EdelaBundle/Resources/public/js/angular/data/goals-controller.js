edelaControllers.controller('GoalsListController', ['$scope', '$http', '$location', function ($scope, $http, $location) {

    $scope.loading = true;
    $scope.newGoal = {};
    $scope.addGoal = function () {
        $http({
            method: 'POST',
            url: 'api/goals',
            data: $scope.newGoal
        }).success(function (data) {
            $location.path('/goals/' + data.id + '/edit');
        });
    };

    $scope.gotoActions = function (id) {
        $location.path('/goals/' + id + '/actions');
    };

    $scope.goals = $http.get('api/goals').success(function (data) {
        $scope.loading = false;
        if (data.length > 0) {
            $scope.$parent.mainClass = 'form-add-my-goal';
        }
        $scope.goals = data;
    });


}]);

edelaControllers.controller('GoalsEditController', ['$scope', '$http', '$routeParams', '$location', '$fileUploader', '$rootScope',
    function ($scope, $http, $routeParams, $location, $fileUploader, $rootScope) {
        $scope.loading = true;

        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,
            url: 'api/goals/images',
            autoUpload: true
        });

        uploader.bind('complete', function (event, xhr, item, response) {
            $scope.goal.images.push(response);
        });

        $http.get('api/goals/' + $routeParams.id).success(function (data) {
            $scope.loading = false;
            $scope.goal = data;
            $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
                {active: false, href: '#goals', caption: data.name},
                {active: true, caption: 'Настройки'},
                {active: false, href: '#goals/' + data.id + '/actions', caption: 'Список задач'},
            ]);
            $scope.uploader.formData.push({ goal: data.id });
        });
        $scope.editGoal = function () {
            var id = $scope.goal.id;
            var goal = {};
            angular.copy($scope.goal, goal);
            delete goal.id;
            $http({
                method: 'PATCH',
                url: 'api/goals/' + id,
                data: {'edit_goal': goal}
            }).success(function (data) {
//                $location.path('/goals/' + $scope.goal.id + '/actions');
            });
        }

        $scope.deleteGoal = function(){
            $http.delete('api/goals/' + $scope.goal.id).success(function(data){
                $location.path('/goals');
            });
        }

    }]);

edelaControllers.controller('GoalsActionsController', ['$scope', '$http', '$routeParams', '$location', '$rootScope', 'actionsManager', 'globalVars', 'tasksManager',
    function ($scope, $http, $routeParams, $location, $rootScope, actionsManager, globalVars, tasksManager) {
        $scope.vars = globalVars.getVars();
        $scope.melodies = [1];
        $scope.loading = true;

        actionsManager.loadActions($routeParams.id).then(function (actions) {
            $scope.actions = actions;
            $scope.loading = false;
        });

        $http.get('api/goals/' + $routeParams.id).success(function (data) {
            $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
                {active: false, href: '#goals', caption: data.name},
                {active: false, href: '#goals/' + data.id + '/edit', caption: 'Настройки'},
                {active: true, href: '#goals/' + data.id + '/actions', caption: 'Список задач'},
            ]);
        });

        $scope.goal = $routeParams.id;

        tasksManager.loadTasks($routeParams.id).then(function (tasks) {
            $scope.tasks = tasks;
        });

        $scope.executeSubaction = function (subaction) {
            console.log(subaction);
        };

        $scope.executeAction = function (action, $event) {
            if (action.progress) {
                return;
            }
            if (action.subactions && action.subactions.length > 0) {
                angular.element($event.target).scope().showSubactions = true;
            } else {
                action.progress = true
            }
        };
        $scope.editAction = function ($event) {
            $rootScope.$broadcast('EDIT_ACTION', $($event.target).scope().action);
            $scope.showEditPopup = true;
        };

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
            goal: $routeParams.id
        };
        $scope.newAction = angular.copy(blankAction);
        $scope.display = { showNew: false };

        $("body").on('keypress', 'textarea.new_action', function(e){
            if (e.keyCode == 13){
                $scope.addAction();
            }
        });

        $scope.addAction = function () {
            $scope.newAction.start_at = new Date().toISOString().slice(0, 10);
            $scope.actions.push(actionsManager.addAction($scope.newAction));
            $scope.newAction = angular.copy(blankAction);
            $scope.display.showNew = false;
        };

        $scope.newTask = {
            createdAt: "2014-07-16T14:48:45+0700",
            done: false,
            is_important: false,
            is_urgent: false,
            is_sms_notification: false,
            parent: 0,
            goal: $routeParams.id
        };

        $scope.addTask = function (parent, $event) {
            if (parent) {
                $scope.newTask.parent = parent.id;
            }
            tasksManager.addTask($scope.newTask).then(function (task) {
                $scope.tasks.push(task);
                $scope.newTask.title = '';
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
    }]);