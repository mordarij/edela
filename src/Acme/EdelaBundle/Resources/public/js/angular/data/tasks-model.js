edelaApp.factory('Task', ['$http', '$rootScope', 'globalVars', '$q', function ($http, $rootScope, globalVars, $q) {
    function Task(taskData) {
        if (taskData) {
            this.setData(taskData);
        }
    };

    Task.prototype = {
        completed: Boolean(this.progress),
        _init: function () {
        },
        changeProgress: function () {

        },
        isToday: function () {
            var today = new Date();
            return this.date && new Date(this.date).setHours(0, 0, 0, 0) == new Date().setHours(0, 0, 0, 0);
        },
        addSubtask: function (title) {
            this.subtasks = this.subtasks || [];
            var subtask = { title: title, progress: 0 };
            this.subtasks.push(subtask);
        },
        addTag: function (title) {
            this.tags = this.tags || [];
            this.tags.push({title: title});
        },
        removeTag: function (tag) {
            var index = this.tags.indexOf(tag);
            if (index > -1) {
                this.tags.splice(index, 1);
            }
        },
        setData: function (taskData) {
            angular.extend(this, taskData);
            this._init();
        },
        execute: function ($event) {
            this.done = !this.done;
            var scope = this;
            $http({
                method: 'POST',
                url: 'api/tasks/' + this.id + '/execute'
            }).success(function (data) {
                if (data.success) {
                    scope.done = data.done;
                }
            });
        },
        editPopup: function ($event) {
            $rootScope.$broadcast('EDIT_TASK', this);
            $('.pop-setting-goals').css('top', $($event.target).parents('.list-heading:first').offset().top);
        },
        createAndSave: function () {
            var deferred = $q.defer();
            if (this.title == '') {
                deferred.reject();
                return deferred.promise;
            }
            var scope = this;

            if (!this.id || String(this.id).substr(0, 1) == '_') {
                this.create().then(function () {
                    scope.saveChanges().then(function () {
                        deferred.resolve();
                    });
                });
            } else {
                scope.saveChanges().then(function () {
                    deferred.resolve();
                });
            }

            return deferred.promise
        },
        saveChanges: function () {
            var deferred = $q.defer();
            var scope = this;
            var d = new Date();
            var curr_date = d.getDate();
            var curr_month = d.getMonth() + 1;
            var curr_year = d.getFullYear();
            this.date = this.date;// || curr_year + "-" + curr_month + "-" + curr_date;
            if (this.date && typeof this.date.month == 'undefined') {
                this.date = { year: this.date.split('-')[0], month: Number(this.date.split('-')[1]), day: Number(this.date.split('-')[2])};

            }

            this.notification_time = this.notification_time || "00:00";
            if (typeof this.notification_time.hour == 'undefined') {
                this.notification_time = { hour: Number(this.notification_time.split(':')[0]), minute: Number(this.notification_time.split(':')[1]) }
            }

            $http({
                method: 'patch',
                url: 'api/tasks/' + this.id,
                data: { task_edit: {
                    name: this.title,
                    goal: this.goal_id,
                    date_at: this.date,
                    is_important: this.is_important,
                    is_urgent: this.is_urgent,
                    is_sms_notification: this.is_sms_notification,
                    notification_time: this.notification_time,
                    note: this.note,
                    tags: this.tags
                }}
            }).success(function (data) {
                scope.setData(data);
                deferred.resolve();
            });

            return deferred.promise
        },
        create: function () {
            var deferred = $q.defer();
            var postData = {
                goal: this.goal || 0,
                name: this.title,
                parent: this.parent || null
            };
            var scope = this;
            $http({
                method: 'POST',
                url: 'api/tasks',
                data: { task_create_short: postData }
            }).success(function (data) {
                scope.id = data.id;
                deferred.resolve();
            }).error(function () {
                deferred.reject();
            });
            return deferred.promise;
        }
    };
    return Task;
}]);


edelaApp.factory('tasksManager', ['$http', '$q', 'Task', '$filter', function ($http, $q, Task, $filter) {
    var tasksManager = {
        _pool: {},
        _retrieveInstance: function (taskId, taskData) {
            var instance = this._pool[taskId];

            if (instance) {
                instance.setData(taskData);
            } else {
                instance = new Task(taskData);
                this._pool[taskId] = instance;
            }

            return instance;
        },
        loadTasks: function (goal_id) {
            var deferred = $q.defer();
            var scope = this;
            $http.get('api/tasks', {params: { goal_id: goal_id }})
                .success(function (TasksArray) {
                    var Tasks = [];
                    TasksArray.forEach(function (taskData) {
                        var Task = scope._retrieveInstance(taskData.id, taskData);
                        Tasks.push(Task);
                    });
                    deferred.resolve(Tasks);
                })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        },
        addTask: function (newTask) {
            var deferred = $q.defer();
            var scope = this;
//            if (newTask.parent) {
//                newTask.title = '';
//                newTask.id = '_' + new Date().getTime();
//                var task = scope._retrieveInstance(newTask.id, newTask);
//                deferred.resolve(task);
//            } else {
//
//                var postData = {
//                    goal: newTask.goal || 0,
//                    name: newTask.title
//                };
//                $http({
//                    method: 'POST',
//                    url: 'api/tasks',
//                    data: { task_create_short: postData }
//                }).success(function (data) {
//                    var task = scope._retrieveInstance(data.id, data);
//                    deferred.resolve(task);
//                }).error(function () {
//                    deferred.reject();
//                });
//            }
            if (newTask.parent) {
                newTask.title = '';
            }
            newTask.id = '_' + new Date().getTime();
            var task = scope._retrieveInstance(newTask.id, newTask);
            if (!newTask.parent){
                task.createAndSave();
            }
            deferred.resolve(task);
            return deferred.promise;
        }
    };
    return tasksManager;
}]);