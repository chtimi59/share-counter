app.controller('log-ctrl-login', function($window, $http, $scope)
{
	$scope.fields={ };
	$scope.busy = false;
	$scope.onSubmit = function() {
		$scope.busy = true;
		$scope.login($scope.fields).then(
            function() { $scope.busy = false; },
			function() { $scope.busy = false; }
        );
	}	
});