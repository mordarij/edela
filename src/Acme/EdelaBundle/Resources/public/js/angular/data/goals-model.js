edelaApp.factory('Goal', ['$http', '$rootScope', function ($http, $rootScope) {
    function Goal(goalData) {
        if (goalData) {
            this.setData(goalData);
        }
    };
    Goal.prototype = {
        setData: function (goalData) {
            angular.extend(this, goalData);
        },
    	editPopup: function ($event) {
            $rootScope.$broadcast('EDIT_GOAL', this);
            $('.pop-setting-goals').css('top', $($event.target).parents('.box:first').offset().top);
        }      
    };
    return Goal;
}]);


edelaApp.factory('goalsManager', ['$http', '$q', 'Goal', '$filter','$rootScope', function ($http, $q, Goal, $filter,$rootScope) {
    var goalsManager = {
        _pool: {},
        _retrieveInstance: function (goalId, goalData) {
            var instance = this._pool[goalId];

            if (instance) {
                instance.setData(goalData);
            } else {
                instance = new Goal(goalData);
                this._pool[goalId] = instance;
            }

            return instance;
        },
        _search: function (goalId) {
            return this._pool[goalId];
        },
        _load: function (goalId, deferred) {
            var scope = this;

            $http.get('ourserver/Actions/' + goalId)
                .success(function (goalData) {
                    var Action = scope._retrieveInstance(goalData.id, goalData);
                    deferred.resolve(Action);
                })
                .error(function () {
                    deferred.reject();
                });
        },
        loadGoals: function(){
            var deferred = $q.defer();
            var scope = this;
            var date = new Date();
            $http.get('api/goals')
                .success(function (goalsArray) {
                    var goals = [];
                    goalsArray.forEach(function (goalData) {
                    	goalData.jsId = goalData.id + date.format('yyyymmdd');
                    	goalData.name=goalData.title;
                        var Goal = scope._retrieveInstance(goalData.jsId, goalData);
                        goals.push(Goal);
                    });
                    deferred.resolve(goals);
                  })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        },
        removeGoal: function (goal) {
            delete this._pool[goal.jsId];

            $http({
                method: 'DELETE',
                url: 'api/goals/' + goal.id
            }).success(function (data) {
            	window.location.reload();
            });
            $rootScope.$broadcast('goals:pool:removed', goal);
        }
    };
    return goalsManager;
}]);