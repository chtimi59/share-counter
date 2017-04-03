app.controller('counters-ctrl', function(Dialog,$document,$scope, $http, $window) {

	$scope.items = [];	
	$scope.busy = false;
    $scope.setBusy = function(v) { $scope.busy=v; }

    $scope.currentTab='LIST';
    $scope.currentCounter = null;
    	
    $scope.showLIST = function () { 
		$scope.currentCounter = null;
		$scope.currentTab='LIST';
		$scope.listCounter();
	}
    $scope.showSETTING = function (counter) {
		$scope.currentCounter = counter;
		$scope.currentTab='SETTINGS';
	}
	$scope.showCOUNTER = function (counter) {
		$scope.currentCounter = counter;
		$scope.currentTab='COUNTER';
	}
	$scope.showWRITEHELP = function (counter) {
		$scope.currentCounter = counter;
		$scope.currentTab='WRITEHELP';
	}
	$scope.showREADHELP = function (counter) {
		$scope.currentCounter = counter;
		$scope.currentTab='READHELP';
	}    
	
	
	$scope.listCounter = function() {
		if ($scope.busy) return;
        $scope.busy = true;
        $http.get($scope.SrvUrl("counters"))
       .then( 
            function(ret){
                $scope.items = ret.data;
                $scope.busy = false;		
            },
            function(resp){ 
                $scope.InternalError(resp);
                $scope.busy = false;
            });  
    }
    
	$scope.addCounter = function() {
		if ($scope.busy) return;
        $scope.busy = true;
		$http.post($scope.SrvUrl("counters")).then(
			function(ret){
                $scope.items = ret.data;
				$scope.busy = false;				
                // update list
                $scope.listCounter();
			},
			function(resp){ 
				$scope.InternalError(resp);
				$scope.busy = false;
			});    
	}
	
	$scope.delCounter = function() {
        if ($scope.busy) return;
        if ($scope.currentCounter == null) return;
        $scope.busy = true;
        
		var msg = $scope.S['COUNTER_CONFIRMDELETION_PRE']+$scope.currentCounter.name+$scope.S['COUNTER_CONFIRMDELETION_POST'];
		Dialog.showDialog( 'question', {
			'class': 'warning_dlg',
            'yes' : $scope.S['DLG_YES'],
            'no' : $scope.S['DLG_NO'],
			'msg': msg }
		).then(
            // ok
			function()
            {
             $http.delete($scope.SrvUrl("counters"),{ params: {'data': { 'cuid':$scope.currentCounter.cuid }}}).then( 
                    function(ret) {
                        $scope.busy = false;
                        $scope.showLIST();                        
                    },
                    function(resp) { 
                        $scope.InternalError(resp);
                        $scope.busy = false;
                    }
                );
			},            
            // decline
			function() { $scope.busy = false; }
		);
		//$scope.$digest();
	}
	
    $scope.showLIST();
			
});