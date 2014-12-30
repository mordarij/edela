edelaApp.factory('Goal', ['$http', '$rootScope', function ($http, $rootScope) {
    function Goal(goalData) {
        if (goalData) {
            this.setData(goalData);
        }
    };

    Goal.prototype = {
        setData: function (goalData) {
            angular.extend(this, goalData);
        }
    };
    return Goal;
}]);


edelaApp.factory('goalsManager', ['$http', '$q', 'Goal', '$filter', function ($http, $q, Goal, $filter) {
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
            $http.get('api/goals')
                .success(function (goalsArray) {
                    var goals = [];
                    goalsArray.forEach(function (goalData) {
                        var Goal = scope._retrieveInstance(goalData.id, goalData);
                        goals.push(Goal);
                    });

                    deferred.resolve(goals);
                })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        }
    };
    return goalsManager;
}]);