edelaApp.factory('Tool', ['$http', '$rootScope', 'globalVars', '$q', 'currentUser', '$controller', function ($http, $rootScope, globalVars, $q, currentUser, $controller) {
    function Tool(toolData) {
        if (toolData) {
            this.setData(toolData);
        }
    };

    Tool.prototype = {
        _init: function () {
            this.controller = window[this.class_name];
        },
        setData: function (toolData) {
            angular.extend(this, toolData);
            this._init();
        },
        isAvailableToInstall: function(){
            return !this.is_enabled && (this.is_available || (this.cost == 0 && this.min_level < currentUser.getLevel().number));
        },
        isAvailableToBuy: function(){
            return !this.is_enabled && this.min_level < currentUser.getLevel().number;
        },
        toggleInstall: function(){
            if (!this.is_enabled && !this.isAvailableToInstall()) return;
            var scope = this;
            $http({
                method: 'PATCH',
                url: 'api/tools/' + this.id,
                data: {
                    is_enabled: true
                }
            }).success(function(response){
                if(response.success){
                    scope.setData(response.data);
                }
            })
        },
        buyExp: function(){
            if (this.is_enabled || this.isAvailableToInstall() || !this.isAvailableToBuy()) return;
            var scope = this;
            $http({
                method: 'PATCH',
                url: 'api/tools/' + this.id,
                data: {
                    buy_exp: true
                }
            }).success(function(response){
                if(response.success){
                    scope.setData(response.data);
                }
            })
        }
    };
    return Tool;
}]);


edelaApp.factory('toolsManager', ['$http', '$q', 'Tool', '$filter', function ($http, $q, Tool, $filter) {
    var toolsManager = {
        _pool: {},
        _retrieveInstance: function (toolId, toolData) {
            var instance = this._pool[toolId];

            if (instance) {
                instance.setData(toolData);
            } else {
                instance = new Tool(toolData);
                this._pool[toolId] = instance;
            }

            return instance;
        },
        loadTools: function (enabledOnly) {
            var deferred = $q.defer();
            var scope = this;
            var url = 'api/tools';
            if (enabledOnly){
                url += '/enabled';
            }
            $http.get(url)
                .success(function (ToolsArray) {
                    var Tools = [];
                    ToolsArray.forEach(function (toolData) {
                        var Tool = scope._retrieveInstance(toolData.id, toolData);
                        Tools.push(Tool);
                    });
                    deferred.resolve(Tools);
                })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        }
    };
    return toolsManager;
}]);