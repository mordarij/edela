edelaControllers.controller('SendErrorController', [ '$scope', '$rootScope', '$window',
    function ($scope, $rootScope, $window) {
        $scope.showPopup = false;
        $scope.error = { text: '' }

        $rootScope.$on('SEND_ERROR_PRESSED', function (response, data) {
            $scope.showPopup = true;
            angular.element($window).scrollTop(0);
            $scope.error.text = $window.getSelection().getRangeAt(0);
        });


    }]);