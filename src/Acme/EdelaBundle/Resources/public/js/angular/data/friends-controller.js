edelaControllers.controller('FriendsListController', ['$scope', '$http', '$location', '$rootScope', 'usersManager',
    function ($scope, $http, $location, $rootScope, usersManager) {

        $scope.loading = true;
        $scope.invite = {};

        usersManager.getFriends().then(function(friends){
            $scope.loading = false;
            $scope.friends = friends;
        });

        $scope.sendInvite = function () {
            usersManager.sendInvite($scope.invite);
            $scope.invite.popup = false;
        };

        $scope.showPopup = function () {
            $scope.invite.popup = true;
            $('.pop-up-add-friend').css("top", Math.max(0, (($(window).height() - $('.pop-up-add-friend').outerHeight()) / 4) +
                $(window).scrollTop()) + "px");
        }

        $scope.gotoUser = function (id) {
            window.location.href = 'id' + id;
        }

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {active: true, href: '#friends', caption: 'Все друзья'},
//            {active: false, href: '#friends', caption: 'Друзья онлайн'},
            {active: false, href: '#/friends/find', caption: 'Возможные друзья'},
            {active: false, click: $scope.showPopup, caption: '+ Пригласить друга'}
        ]);
    }]);

edelaControllers.controller('FriendsProfileController', ['$scope', '$http', '$location', '$rootScope', 'usersManager',
    function ($scope, $http, $location, $rootScope, usersManager) {

    }]);

edelaControllers.controller('FriendsFindController', ['$scope', '$http', '$location', '$rootScope', 'usersManager',
    function ($scope, $http, $location, $rootScope, usersManager) {
        $scope.friends = [];
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

        $scope.users = [];
        $scope.criteria = {name: ''}

        $scope.find = function(){
            $http.get('api/friends/find?name=' + $scope.criteria.name).success(function(data){
                $scope.users = data;
            })
        }


        usersManager.getFriends().then(function(data){

            for (var i in data){
                if (!data.hasOwnProperty(i)) continue;
            }
            $scope.friends.push(data[i].user.id);
        });

        $scope.addFriend = function(user){
            usersManager.addFriend(user);
            $scope.friends.push(user.id);
        }

        $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
            {active: false, href: '#friends', caption: 'Все друзья'},
//            {active: false, href: '#friends', caption: 'Друзья онлайн'},
            {active: true, href: '#/friends/find', caption: 'Возможные друзья'},
            {active: false, click: $scope.showPopup, caption: '+ Пригласить друга'}
        ]);

    }]);