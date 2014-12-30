edelaApp.factory('User', ['$http', '$rootScope', 'globalVars', '$q', function ($http, $rootScope, globalVars, $q) {
    function User(userData) {
        if (userData) {
            this.setData(userData);
        }
    };

    User.prototype = {
        _init: function () {
        },
        setData: function (userData) {
            angular.extend(this, userData);
            this._init();
        }
    };
    return User;
}]);


edelaApp.factory('usersManager', ['$http', '$q', 'User', '$filter', '$rootScope', function ($http, $q, User, $filter, $rootScope) {
    var usersManager = {
        _pool: {},
        _retrieveInstance: function (userId, userData) {
            var instance = this._pool[userId];

            if (instance) {
                instance.setData(userData);
            } else {
                instance = new User(userData);
                this._pool[userId] = instance;
            }

            return instance;
        },
        _search: function (userId) {
            return this._pool[userId];
        },
        sendInvite: function (invite) {
            var defer = $q.defer();
            $http({
                method: 'POST',
                url: 'api/friends/invite',
                data: invite
            }).success(function () {
                defer.resolve();
            }).error(function () {
                defer.reject();
            });
            return defer.promise;
        },
        getFriends: function(){
            var defer = $q.defer();
            $http.get('api/friends').success(function(data){
                defer.resolve(data);
            }).error(function(){
                defer.reject();
            });
            return defer.promise;
        },
        addFriend: function(user){
            $http.post('api/friends/add/' + user.id).success(function(){
                $rootScope.$broadcast('FRIENDS_UPDATED');
            });
        }



    };
    return usersManager;
}]);