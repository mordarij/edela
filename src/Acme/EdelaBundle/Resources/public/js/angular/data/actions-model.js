edelaApp.factory('Action', ['$http', '$rootScope', 'globalVars', 'calendar', '$q', function ($http, $rootScope, globalVars, calendar, $q) {
    function Action(actionData) {
        if (actionData) {
            this.setData(actionData);
        }
    }

    Action.prototype = {
        completed: Boolean(this.progress),
        _init: function () {
            this.progressHour = this.progress < 3600 ? null : Math.floor(this.progress / 60 / 60);
            this.progressMinute = this.progress < 60 ? null : Math.floor((this.progress - (this.progressHour * 60)) / 60);
            this.progressSecond = !this.progress ? null : this.progress - (this.progressHour * 60 * 60) - (this.progressMinute * 60 );
            this.completed = Boolean(this.progress);
            //console.log(this.title, this.completed);
        },
        isExecutable: function () {
            var today = new Date().setHours(0, 0, 0, 0);
            return today == calendar.date.setHours(0, 0, 0, 0);
        },
        changeProgress: function () {
            if (this.subactions && this.subactions.length > 0 && globalVars.getVars().action_types[this.action_type_id].tkey == 'done') {
                this.showSubactions = true;
                return;
            }
            if (globalVars.getVars().action_types[this.action_type_id].tkey == 'time') {
                this.progress = Number((this.progressHour || 0) * 60 * 60) + Number((this.progressMinute || 0)* 60) + Number(this.progressSecond || 0);
            }
            if (globalVars.getVars().action_types[this.action_type_id].tkey == 'text') {
                this.progress = this.progress_note.length;
            }
            if (globalVars.getVars().action_types[this.action_type_id].tkey == 'done') {
                this.progress = Number(!this.progress);
            }
            if (globalVars.getVars().action_types[this.action_type_id].tkey == 'number') {
                this.progress = Number(this.progress);
            }
            if (this.progress && !this.completed) {
                this.done++;
                this.completed = true;
            } else if (!this.progress && this.completed) {
                this.done--;
                this.completed = false;
            }
            this.execute();
        },
        showSubactions: false,
        showNotes: false,
        addSubaction: function (title) {        	
        	if(title && title!="" && title!="Название"){
        		this.subactions = this.subactions || [];
        		var subaction = { title: title, progress: 0 };
        		this.subactions.push(subaction);
        	}
        },
        removeSubaction: function (subaction) {
    		this.subactions = this.subactions || [];    	    
            var index = this.subactions.indexOf(subaction);
            if (index > -1) {
                this.subactions.splice(index, 1);
            }            
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
        setData: function (actionData) {
//            for (var key in actionData){
//                if (actionData.hasOwnProperty(key) && this.hasOwnProperty(key) && typeof actionData[key] != 'function' && typeof this[key] != 'undefined'){
//                    delete this[key];
//                }
//            }
            angular.extend(this, actionData);
            this._init();
        },
        setPosition: function(position){
            if (this.position == position) return;

            this.position = position;
            this.saveChanges();
        },
        startAgain: function () {
        	this.progress=0;
        	this.done=-1;
            this.execute();
            this.action_time_start="";
            this.saveChanges();
        },
        toggleDelay: function () {
            this.start_time = this.isDelayed() ? new Date() : null;
            this.saveChanges();
        },
        isDelayed: function () {
            var date = Date.parse(this.start_time);
            return isNaN(date) || date > (new Date());
        },
        isDayEnabled: function (day) {
            return this.periodicity & (1 << day);
        },
        toggleDay: function (day) {
            if (this.periodicity_interval > 0){
                return;
            }
            if (this.isDayEnabled(day)) {
                this.periodicity &= ~(1 << day);
            } else {
                this.periodicity |= (1 << day);
            }
        },
        toggleDayli: function () {
            if (this.periodicity_interval > 0){
                return;
            }
            if (this.periodicity < 127) {
                this.periodicity = 127;
            } else {
                this.periodicity = 0;
            }
        },
        execute: function () {
            if (!this.isExecutable()) return;
            if (this.subactions && this.subactions.length > 0 && globalVars.getVars().action_types[this.action_type_id].tkey == 'done') {
                this.showSubactions = true;
            } else {
//                this.progress = Number(!this.progress);
//                this.done = Number(this.done) + (this.progress ? 1 : -1);
                $http({
                    method: 'POST',
                    url: 'api/actions/' + this.id + '/execute',
                    data: {
                        result: this.progress,
                        note: this.progress_note || false
                    }
                }).success(function (data) {
                    if (data.success) {
//                    action.done += data.progress;
//                    action.progress = data.progress;
                    }
                });
                this._init();
            }
        },
        executeSub: function (subaction) {
            var action = this;
            if (!this.isExecutable()) return;
            $http({
                method: 'POST',
                url: 'api/subactions/' + subaction.id + '/execute'
            }).success(function (data) {
                if (data.success) {
                    subaction.progress = data.progress;
                    if (!data.progress && action.progress) {
                        action.progress = false;
                        action.done--;
                        return;
                    }
                    var total = true;
                    for (var i in action.subactions){
                        if (!action.subactions.hasOwnProperty(i)) continue;
                        total = total && action.subactions[i].progress;
                    }
                    if (!action.progress && total){
                        action.progress = true;
                        action.done++;
                        action.showSubactions=false;
                    }
                }
            })
        },
        editPopup: function ($event) {
            $rootScope.$broadcast('EDIT_ACTION', this);
            $('.pop-setting-goals').css('top', $($event.target).parents('.box:first').offset().top);
        },
        getJointInfo: function () {
            var deffered = $q.defer();
            $http.get('api/actions/' + this.id + '/joint').success(function (data) {
                deffered.resolve(data);
            });

            return deffered.promise;
        },
        remove: function () {

        },
        saveChanges: function () {
            var deffered = $q.defer();
            var scope = this;

            this.action_time = this.action_time || "00:00";
            if (typeof this.action_time.hour == 'undefined') {
                this.action_time = { hour: Number(this.action_time.split(':')[0]), minute: Number(this.action_time.split(':')[1]) }
            }

            this.action_time_start = this.action_time_start || "00:00";
            if (typeof this.action_time_start.hour == 'undefined') {
                this.action_time_start = { hour: Number(this.action_time_start.split(':')[0]), minute: Number(this.action_time_start.split(':')[1]) }
            }

            this.action_time_finish = this.action_time_finish || "00:00";
            if (typeof this.action_time_finish.hour == 'undefined') {
                this.action_time_finish = { hour: Number(this.action_time_finish.split(':')[0]), minute: Number(this.action_time_finish.split(':')[1]) }
            }

            this.notification_time = this.notification_time || "00:00";
            if (typeof this.notification_time.hour == 'undefined') {
                this.notification_time = { hour: Number(this.notification_time.split(':')[0]), minute: Number(this.notification_time.split(':')[1]) }
            }


            $http({
                url: 'api/actions/' + this.id,
                method: 'patch',
                data: {
                    user_action_edit: {
                        periodicity: this.periodicity,
                        periodicity_interval: this.periodicity_interval,
                        is_private: this.is_private,
                        is_time_report: this.is_time_report,
                        is_important: this.is_important,
                        is_sms_notification: this.is_sms_notification,
                        is_email_notification: this.is_email_notification,
                        notification_time: this.notification_time,
                        position: this.position,
                        action: {
                            title: this.title,
                            repeat_amount: this.repeat_amount,
                            goal: this.goal.id,
                            subactions: this.subactions,
                            action_type: Number(this.action_type_id),
                            action_type_title: this.action_type_title,
                            action_dynamic_type: this.dynamic_type_id,
                            action_time: this.action_time,
                            action_time_start: this.action_time_start,
                            action_time_finish: this.action_time_finish,
                            note: this.note,
                            tags: this.tags
                        }
                    }
                }
            }).success(function (data) {
                scope.setData(data);
                deffered.resolve()
            });

            return deffered.promise;
        }
    };
    return Action;
}]);


edelaApp.factory('actionsManager', ['$http', '$q', 'Action', '$filter', '$rootScope', function ($http, $q, Action, $filter, $rootScope) {
    return {
        _pool: {},
        _retrieveInstance: function (actionId, actionData) {
            var instance = this._pool[actionId];

            if (instance) {
                instance.setData(actionData);
            } else {
                instance = new Action(actionData);
                this._pool[actionId] = instance;
            }

            return instance;
        },
        _search: function (actionId) {
            return this._pool[actionId];
        },
        loadAction: function (actionId) {
            var scope = this;
            var date = new Date();
            var deferred = $q.defer();
            $http.get('api/actions/' + actionId)
                .success(function (actionData) {
                    actionData.jsId = actionData.id + date.format('yyyymmdd');
                    var Action = scope._retrieveInstance(actionData.jsId, actionData);
                    deferred.resolve(Action);
                })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        },
        loadActions: function (goal_id, date) {
            if (typeof date === 'undefined') {
                var date = new Date();
            }
            var deferred = $q.defer();
            var scope = this;
            $http.get('api/actions', {params: { goal_id: goal_id, date: date ? date.format('yyyy-mm-dd') : null }})
                .success(function (ActionsArray) {
                    var Actions = [];
                    ActionsArray.forEach(function (actionData) {
                        actionData.jsId = actionData.id + date.format('yyyymmdd');
                        var Action = scope._retrieveInstance(actionData.jsId, actionData);
                        Actions.push(Action);
                    });

                    deferred.resolve(Actions);
                })
                .error(function () {
                    deferred.reject();
                });
            return deferred.promise;
        },
        addAction: function (newAction) {
//          var deferred = $q.defer();
          var scope = this;
          newAction.jsId = Math.floor(Math.random() * 1000) + new Date().format('yyyymmdd');
          var action = scope._retrieveInstance(newAction.jsId, newAction);
          newAction.position = Object.keys(this._pool).length;
          $http({
              method: 'POST',
              url: 'api/actions',
              data: {
                  action_create_short: {
                      goal: newAction.goal,
                      start_at: newAction.start_at,
                      title: newAction.title,
                      position: newAction.position
                  }
              }
          }).success(function (data) {
              action.setData(data);
//              var action = scope._retrieveInstance(data.id, data);
//              deferred.resolve(action);
          }).error(function () {
//              deferred.reject();
          });
          return action;
//          return deferred.promise;
      },
      removeAction: function (action) {
            delete this._pool[action.jsId];

            $http({
                method: 'DELETE',
                url: 'api/actions/' + action.id
            }).success(function (data) {
            	
            });
            $rootScope.$broadcast('actions:pool:removed', action);
        }
    };
}]);