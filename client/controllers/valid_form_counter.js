app.controller('valid_form_counter', function($window, $http, $scope)
{
	$scope.counter={};
	
	$scope.setRKey = function() { 
		$('input[name=wkey]').attr('readonly',false);
		$scope.counter.rkey = guid();
		$('input[name=wkey]').attr('readonly',true);
	}
	
	$scope.setWKey = function() { 
		$('input[name=wkey]').attr('readonly',false);
		$scope.counter.wkey = guid();
		$('input[name=wkey]').attr('readonly',true);
	}
	
	$scope.t = {};
	
	$scope.t.name = {};
	$scope.t.name.fnc = function() {
		$scope.t.name.err = ($scope.counter.name!="")?null:S['COUNTER_ERROR_EMPTYNAME'];
	}
	
	$scope.t.max = {};
	$scope.t.max.fnc = function(MAX) {
		$scope.counter.max;
		max = parseInt($scope.counter.max);
		$scope.t.max.err = null;
		if (MAX!=0) {
			if ((max != $scope.counter.max)||(max<1)||(max > MAX)) $scope.t.max.err = S['COUNTER_ERROR_MAXRECORD']+MAX;
		} else {
			if ((max != $scope.counter.max)||(max<0)) $scope.t.max.err = S['COUNTER_ERROR_MAXRECORDILLIMITED'];
		}
	}
	
	$scope.submitForm = function() {
		if ($scope.t.max.err != null || $scope.t.name.err!=null) return;
		$http.post($scope.baseUrl + "php/countersapi.php",  { 'action' : 'updateCounter', 'data':$scope.counter }).
			then(
				function(resp) { $window.location.href = $scope.counter.listurl; }
				,function(resp) { console.error(resp.data); }
			);
	}

});