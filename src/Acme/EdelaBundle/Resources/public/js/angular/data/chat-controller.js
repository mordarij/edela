edelaControllers.controller('ChatController', [ '$scope', 'socket', '$rootScope',
    function ($scope, socket, $rootScope) {
        function getFriends(){
            socket.emit('contactlist', function (friends) {
                console.log('cont send');
                console.log(friends);
                $scope.friends = friends;
            });
        }
        socket.on('connect', function () {
            socket.emit('auth', { user_id: currentUserId }, function (fullname) {
                $scope.current = { fullname: fullname };
                getFriends();
                socket.emit('messages:unread', function (messages) {
                    $scope.messages = messages;
                });
            });
        });

        $rootScope.$on('FRIENDS_UPDATED', getFriends);



        $scope.onlineFriends = function () {
            var online = [];
            for (var i in $scope.friends) {
                if (!$scope.friends.hasOwnProperty(i)) continue;
                if ($scope.friends[i].is_online) {
                    online.push($scope.friends[i]);
                }
            }
            return online;
        }

        $scope.currentChatMessage = function (message) {
            return message.sender_id == $scope.currentChat.id || message.receiver_id == $scope.currentChat.id;
        }

        $scope.selectChat = function (friend) {
            if ($scope.currentChat == friend) {
                $scope.currentChat = false;
            } else {
                $scope.currentChat = friend;
            }
        }

        socket.on('friends:online', function (user_id, is_online) {
            $scope.friends[user_id].is_online = is_online;
        });

        socket.on('messages:new', function (message) {
            console.log('received', message);
            $scope.messages.push(message);
        });

        $scope.newMessage = {text: ''}
        $scope.sendMessage = function () {
            if (!$scope.currentChat || !$scope.newMessage.text) return;
            $scope.sending = true;
            socket.emit('messages:send', { receiver: $scope.currentChat.id, text: $scope.newMessage.text }, function (message) {
                $scope.messages.push(message);
                $scope.sending = false;
                $scope.newMessage.text = '';
            });
        }

        $scope.readMessage = function (message) {
            console.log('read', message.id);
            if (message.receiver_id == currentUserId) {
                socket.emit('message:read', message.id, function () {
                    message.is_read = 1;
                });
            }
        }

    }
])
;