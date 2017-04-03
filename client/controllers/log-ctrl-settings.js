app.controller('log-ctrl-settings', function($window, $http, $scope)
{        
    /* General field validation */
    $scope.valid = false;    
    $scope.validate = function() {
       $scope.valid = false;
       for(key in $scope.fields) {
          if ($scope.fields[key].isError) return;
       }
       $scope.valid = true;
    }
    
    /* Name field validation */
    var Name = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() {
            this.isError = false;
            this.msg = null;
            if (this.value===null || this.value===undefined || this.value=='') { 
                this.isError = true;
                if (this.changeCount>0) this.msg=$scope.S['LOGIN_ERROR_INVALIDNAME'];
                return;
            }
        };
        this.test();
    };
    $scope.$watch("fields.name.value",  function(newValue, oldValue) { 
        $scope.fields.name.changeCount++;
        $scope.fields.name.test();
        $scope.validate();
    } );
    
    
    /* Password field validation */
    var Pw = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() { 
            this.isError = false;
            this.msg = null;
            if (this.value===null || this.value===undefined || this.value=='') { 
                this.isError = true;
                if (this.changeCount>0) this.msg=$scope.S['LOGIN_ERROR_INVALIDPASSWORD'];
                return;
            }
        };
        this.test();
    };
    $scope.$watch("fields.pw.value",  function() { 
        $scope.fields.pw.changeCount++;
        $scope.fields.pw.test();
        $scope.fields.pw2.test();
        $scope.validate();
    } );
    
    /* Password2 field validation */
    var Pw2 = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() {
            this.isError = false;
            this.msg = null;
            if (this.value===null || this.value===undefined || this.value=='') { 
                this.isError = true;
                if (this.changeCount>0) this.msg=$scope.S['LOGIN_ERROR_INVALIDPASSWORD'];
                return;
            }
            if ($scope.fields.pw.value != $scope.fields.pw2.value) { 
                this.isError = true;
                this.msg=$scope.S['LOGIN_ERROR_INVALIDPASSWORDNOMATCH'];
                return;
            }
        };
        this.test();
    };
    $scope.$watch("fields.pw2.value",  function(newValue, oldValue) { 
        var c = $scope.fields.pw2.changeCount;
        $scope.fields.pw2.changeCount++;
        $scope.fields.pw2.test();
        $scope.validate();
    } );
    
    /* Email field validation */
    var Email = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() { 
            this.isError = false;
            this.msg = null;
            if (this.value===null || this.value===undefined || this.value=='') { 
                this.isError = true;
                if (this.changeCount>0) this.msg=$scope.S['LOGIN_ERROR_INVALIDEMAIL'];
                return;
            }
        };
        this.test(true);
    };
    $scope.$watch("fields.email.value",  function(newValue, oldValue) { 
        $scope.fields.email.changeCount++;
        $scope.fields.email.test();
        $scope.validate();
    } );
    
    
    
    
    
    /* Initialization */
    $scope.fields = {}
    $scope.fields.name = new Name(($scope.user)?$scope.user.name:null);
    $scope.fields.pw = new Pw(null);
    $scope.fields.pw2 = new Pw2(null);    
    if ($scope.verif) {        
        $scope.fields.email = new Email($scope.verif.EMAIL);
    } else {
        $scope.fields.email = new Email(($scope.user)?$scope.user.email:null);
    }
    $scope.validate();    
    
    $scope.busy = false;

    $scope.onSubmit = function()
    {
        for(key in $scope.fields) {
          $scope.fields[key].changeCount++;
          $scope.fields[key].test();
        }        
        $scope.validate();
        
        if (!$scope.valid) return;
        $scope.busy = true;
        if ($scope.verif) {
            $http.post($scope.SrvUrl("log"), 
                {'action':'add', 'data':
                    {
                        'tmp_uuid':$scope.verif.UUID,
                        'lang':$scope.lang,
                        'name':$scope.fields.name.value,
                        'email':$scope.fields.email.value,
                        'pw':$scope.fields.pw.value
                    }
                }
            ).then( 
                    function(ret){
                        $scope.showInfo($scope.S['LOGIN_SUCESS_NEWUSER']+$scope.fields.name.value);
                        $scope.login({"name":$scope.fields.name.value, "pw":$scope.fields.pw.value})
                        $scope.busy = false;
                    },
                    function(resp){ 
                        $scope.InternalError(resp);
                        $scope.busy = false;
                    }
            );
        } else {
            $http.post($scope.SrvUrl("log"), 
                {'action':'update', 'data':
                    {
                        'lang':$scope.lang,
                        'name':$scope.fields.name.value,
                        'email':$scope.fields.email.value,
                        'pw':$scope.fields.pw.value
                    }
                }
            ).then( 
                    function(ret){
                        $scope.login({"name":$scope.fields.name.value, "pw":$scope.fields.pw.value})
                        $scope.busy = false;
                    },
                    function(resp){ 
                        $scope.InternalError(resp);
                        $scope.busy = false;
                    }
            );
        }
        
    }    

});