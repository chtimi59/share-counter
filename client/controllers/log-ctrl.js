app.controller('log-ctrl', function($window, $http, $scope)
{
    $scope.height={ 'LOGIN': 252, 'SETTINGS': 414, 'REGISTER': 252, 'FORGET': 252};
    $scope.currentTab='LOGIN';
    $scope.showLOGIN    = function () { $scope.currentTab='LOGIN'; }
    $scope.showFORGET   = function () { $scope.currentTab='FORGET'; }
    $scope.showREGISTER = function () { $scope.currentTab='REGISTER'; }
    $scope.showSETTINGS = function () { $scope.currentTab='SETTINGS'; }
    
    $scope.busy = false;
    $scope.getTmpUserData = function(tmp_uuid) {
        $scope.busy = true;
        $http.get($scope.SrvUrl("log"),{params: 
            {'action':'register', 'data': { 'tmp_uuid':tmp_uuid }}
        }).then( 
            function(ret){
                $scope.verif = ret.data;
                $scope.showSETTINGS();
                $scope.busy = false;
            },
            function(resp){ 
                $scope.InternalError(resp);
                $scope.showREGISTER();
                $scope.busy = false;
            }
        );
    }
});