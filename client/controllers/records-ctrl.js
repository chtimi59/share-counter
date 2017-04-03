app.controller('records-ctrl', function(Dialog,$document,$scope, $http, $window) {

	$scope.items = [];	

	/*
	$scope.setRecord = function(counter) {		
		$window.location.href = counter.seturl;
	}
		
	$scope.addCounter = function() {
		$http.post($scope.baseUrl + "php/countersapi.php",  { 'action' : 'createCounter' }).
		then( function(resp) { 
			if (resp.data.status==1) { 
				Dialog.showDialog('message', {'msg': S['COUNTER_ERROR_MAXIMUMREACHED'],'ok': $scope.S['DLG_OK'] })
			} else {
				$http.get($scope.baseUrl + "php/countersapi.php").
					then( 
						function(resp) { $scope.items = resp.data; }
						,function(resp) { console.error(resp.data); }
					);
			}
		});
	}
	
	$scope.delCounter = function(counter) {
		var msg = S['COUNTER_CONFIRMDELETION_PRE']+counter.name+S['COUNTER_CONFIRMDELETION_POST'];
		Dialog.showDialog( 'question', {
			'class': 'warning_dlg',
            'yes' : $scope.S['DLG_YES'],
            'no' : $scope.S['DLG_NO'],
			'msg': msg }
		).then(
			
			function() {
				$http.delete($scope.baseUrl + "php/countersapi.php",  { 'data' : counter }).
				then(
					function(resp) { $window.location.href = counter.listurl; }
					,function(resp) { console.error(resp.data); }
				);
			},
			
			function() {
				//console.log('reject');
			}
		);
		scope.$digest();
	}
	*/
	/*$http.get($scope.baseUrl + "php/recordsapi.php").
		then( 
			function(resp) { $scope.items = resp.data; }
			,function(resp) { console.error(resp.data); }
		);*/

});