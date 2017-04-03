app.controller('log-ctrl-register', function($window, $http, $scope)
{
    $scope.fields={ };
    
    $scope.busy = false;
    
    $scope.valid = false;
    $scope.validate = function() { 
       var valid = true;
       if ($scope.fields.email===undefined) { valid = false }
       if ($scope.fields.email=='') { valid = false }
       $scope.valid = valid;
    };
    
    $scope.$watch("fields.email", $scope.validate);

    $scope.onSubmit = function()
    {
        $scope.validate();
        if (!$scope.valid) return;
        $scope.busy = true;
        $http.post($scope.SrvUrl("log"), {'action':'register', 'data':{'lang':$scope.lang, 'email':$scope.fields.email }})
               .then( 
                    function(ret){
                        $scope.showLOGIN();
                        $scope.showInfo($scope.S['LOGIN_REGISTER_MAILSENTO']+$scope.fields['email']);
                    },
                    function(resp){ 
                        $scope.InternalError(resp);
                        $scope.busy = false;
                    });
    }    
});