edelaControllers.controller('GoalsListController', ['$scope', '$http', '$location','goalsManager', function ($scope, $http, $location, goalsManager) {
    $scope.loading = true;
    $scope.showEditPopupGoalAdd = false;
    $scope.showEditPopupGoal = false;
    
    $scope.addGoalPopup = function(){
    	$('.pop-setting-goals-add').css('top', 200);
 	   $scope.settings = {tab: 'tab1'};
       $scope.showEditPopupGoalAdd = true;
    }
    $scope.cancelAdd = function () {
        $scope.showEditPopupGoalAdd = false;
    };
    
   goalsManager.loadGoals().then(function (goals) {
    	$scope.loading = false;
    	if (goals.length > 0) {
			$scope.$parent.mainClass = 'form-add-my-goal';
		}
        $scope.goals = goals;
        $("#hover").removeClass("active");
        $("#hover1").addClass("active");
    });  
}]);
edelaControllers.controller('GoalsAddController', ['$scope', '$http', '$routeParams', '$location', '$fileUploader', '$rootScope','globalVars', 'goalsManager',
    function ($scope, $http, $routeParams, $location, $fileUploader, $rootScope, globalVars, goalsManager) {
		$scope.newGoal = {name:'Новая цель'};
		$scope.newGoal.images=new Array();
	
		var uploader = $scope.uploader = $fileUploader.create({
			scope: $scope,
			url: 'api/goals/images',
			autoUpload: true
		});
		$scope.addGoal = function () {
            $http({
                method: 'POST',
                url: 'api/goals',
                data: $scope.newGoal
            }).success(function (data) {   
            	if(data['error'])alert('Такая цель уже существует');
            	else
            	window.location.reload();
            });
        };
		uploader.bind('complete', function (event, xhr, item, response) {    
			if(!response['id'])alert('Изобрежание слишком большое для загрузки. Желательный размер 480x320.');
			else{
				$scope.newGoal.images.push(response); 
				$scope.addGoal();
			}
        });  
}]);
    
edelaControllers.controller('GoalsEditController', ['$scope', '$http', '$routeParams', '$location', '$fileUploader', '$rootScope','globalVars', 'goalsManager',
    function ($scope, $http, $routeParams, $location, $fileUploader, $rootScope, globalVars, goalsManager) {
        
		var beforeEdit;
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,
            url: 'api/goals/images',
            autoUpload: true
        });
                       
        $rootScope.$on('EDIT_GOAL', function (response, data) {
            $scope.settings = {tab: 'tab1'};
            $scope.vars = globalVars.getVars();
            $scope.goal = goalsManager._retrieveInstance(data.jsId, data); 
            beforeEdit = angular.copy($scope.goal);
            $scope.showEditPopupGoal = true;
           // $scope.settingsBriefly = true;           
            $scope.uploader.formData.push({ goal: $scope.goal.id });
            uploader.bind('complete', function (event, xhr, item, response) {        	
            	$scope.goal.images.push(response);        	        
            });              
        });
     
       /* $http.get('api/goals/' + $routeParams.id).success(function (data) {
            $scope.loading = false;
            $scope.goal = data;
            $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
                {active: false, href: '#goals', caption: data.name},
                {active: true, caption: 'Настройки'},
                {active: false, href: '#goals/' + data.id + '/actions', caption: 'Список задач'},
            ]);
            $scope.uploader.formData.push({ goal: data.id });
        });*/
      
        $scope.cancelEdit = function () {
            $scope.goal.setData(beforeEdit);            
            $scope.showEditPopupGoal = false;
        };
                                       
        $scope.editGoal = function () {
            var id = $scope.goal.id;
            var goal = {};
            angular.copy($scope.goal, goal);
            delete goal.id;
            delete goal.jsId;
            delete goal.actions;
            delete goal.title;
            $http({
                method: 'patch',
                url: 'api/goals/' + id,
                data: {edit_goal: goal}
            }).success(function (data) {         
            	loadGoals();
            });
        }
        $scope.deleteGoal = function(){
        	 goalsManager.removeGoal($scope.goal);
        	 $scope.showEditPopupGoal = false;                    	 
        }       
        
        function loadGoals(){ 
        	 $scope.goals={};
        	 $scope.loading = true;
        	 $scope.showEditPopupGoal = false;            
        	 goalsManager.loadGoals().then(function (goals) {

        		 $scope.loading = false;
        	    	if (goals.length > 0) {
        				$scope.$parent.mainClass = 'form-add-my-goal';
        			}
        	        $scope.goals = goals;
        	        $("#hover").removeClass("active");
        	        $("#hover1").addClass("active");
        	    });  
        }
        
    }]);

edelaControllers.controller('GoalsActionsController', ['$scope', '$http', '$routeParams', '$location', '$rootScope', 'actionsManager', 'goalsManager','globalVars', 'tasksManager',
    function ($scope, $http, $routeParams, $location, $rootScope, actionsManager, goalsManager, globalVars, tasksManager) {
        $scope.vars = globalVars.getVars();
        $scope.melodies = [1];
        $scope.loading = true;
        
        $rootScope.$on('actions:pool:removed', function (response, data) {        
        	actionsManager.loadActions($scope.goal.id).then(function (actions) {
        		$scope.actions = actions;
        		$scope.loading = false;
        	});
        });
        
        $rootScope.$on('EDIT_GOAL', function (response, data) {
        	 
        	$scope.goal = goalsManager._retrieveInstance(data.jsId, data);               
        
        	actionsManager.loadActions($scope.goal.id).then(function (actions) {
        		$scope.actions = actions;
        		$scope.loading = false;
        	});
        
      /*  $http.get('api/goals/' + $routeParams.id).success(function (data) {
            $rootScope.$broadcast('BREADCRUMBS_CHANGED', [
                {active: false, href: '#goals', caption: data.name},
                {active: false, href: '#goals/' + data.id + '/edit', caption: 'Настройки'},
                {active: true, href: '#goals/' + data.id + '/actions', caption: 'Список задач'},
            ]);
        });
		*/        	
        });

        /*tasksManager.loadTasks($routeParams.id).then(function (tasks) {
            $scope.tasks = tasks;
        });
         */
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
            start_time: "2014-07-31T00:00:00+0700"
          };
        $scope.newAction = angular.copy(blankAction);
        $scope.display = { showNew: false };

        $("body").on('keypress', 'textarea.new_action', function(e){
            if (e.keyCode == 13){
                $scope.addAction();
            }
        });

        $scope.addAction = function () {
        	if($scope.newAction.title && $scope.newAction.title!="" && $scope.newAction.title!="Название..."){
        	$scope.newAction.goal = $scope.goal.id;
            $scope.newAction.start_at = new Date().toISOString().slice(0, 10);
            $scope.actions.push(actionsManager.addAction($scope.newAction));
            $scope.newAction = angular.copy(blankAction);
            $scope.display.showNew = false;
        	}else{
        		alert("Введите название ежедненвого дела");
        	}
        };

       /* $scope.newTask = {
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
        }*/
    }]);