app.controller('gallery-ctrl', function(Dialog,$document,$scope, $http, $window) {
	$scope.items = [];
	$scope.filter = { 'reader' : true, 'writer' : false };
	$scope.currentTab = 'GALLERY';
	$http.get($scope.baseUrl + "php/galleryapi.php").
		then(
			function(resp) { $scope.items = resp.data; console.log($scope.items); }
			,function(resp) { console.log(resp.data); }
		);
	
	$scope.tryIt = function(element) {
		$window.location.href = element.default_url;
	}
	$scope.installIt = function(element) {
		Dialog.showDialog( 'selectcounter', {
			'title': 'install',
            'ok': $scope.S['DLG_OK'],
			'msg': 'asd' }
		).then(function() {
			console.log('sss');
		});
	}
	$scope.uninstallIt = function(element) {
		Dialog.showDialog( 'question', {
			'title': 'uninstall',
            'yes' : $scope.S['DLG_YES'],
            'no' : $scope.S['DLG_NO'],
			'msg': 'asd' }
		).then(function() {
			console.log('sss');
		});
	}
});