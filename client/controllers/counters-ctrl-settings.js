app.controller('counters-ctrl-settings', function($window, $http, $scope)
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
                if (this.changeCount>0) this.msg=$scope.S['COUNTER_ERROR_EMPTYNAME'];
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
    
    
    /* Max field validation */
    var Max = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() {
            this.isError = false;
            this.msg = null;
            if ($scope.user.max_record==0) {
                // unlimited
                if (this.value && (this.value != parseInt(this.value) || this.value<0)) { 
                    this.isError = true;
                    if (this.changeCount>0) this.msg=$scope.S['COUNTER_ERROR_MAXRECORDILLIMITED'];
                    return;
                }
            } else {
                // limited
                if (this.value===null || this.value===undefined ||
                    this.value != parseInt(this.value) || this.value<=0 || this.value>$scope.user.max_record)
                { 
                    this.isError = true;
                    if (this.changeCount>0) this.msg=$scope.S['COUNTER_ERROR_MAXRECORD']+$scope.user.max_record;
                    return;
                }
            }
        };
        this.test();
    };
    $scope.$watch("fields.max.value",  function(newValue, oldValue) { 
        $scope.fields.max.changeCount++;
        $scope.fields.max.test();
        $scope.validate();
    } );

    
    /* field without validation */
    var UntestedField = function (v) {
        this.value = v;
        this.msg = null;
        this.isError = false;
        this.changeCount = -1;
        this.test = function() { };
    };
    
    $scope.setRKey = function() {  $scope.fields.rkey.value = guid(); }
	$scope.setWKey = function() {  $scope.fields.wkey.value = guid(); }
    
    /* Initialization */
    $scope.fields = {}
    $scope.fields.name = new Name($scope.currentCounter.name);
    $scope.fields.max  = new Max($scope.currentCounter.max);
    $scope.fields.wkey = new UntestedField($scope.currentCounter.wkey);
    $scope.fields.rkey = new UntestedField($scope.currentCounter.rkey);
    
    $scope.validate();    
		
    $scope.onSubmit = function()
    {
        for(key in $scope.fields) {
          $scope.fields[key].changeCount++;
          $scope.fields[key].test();
        }        
        $scope.validate();
        
        if (!$scope.valid) return;
        if ($scope.busy) return;
        $scope.setBusy(true);
                   
        $http.put($scope.SrvUrl("counters"), 
         {'data':
            {
                'cuid':$scope.currentCounter.cuid,
                'name':$scope.fields.name.value,
                'max':$scope.fields.max.value,
                'wkey':$scope.fields.wkey.value,
                'rkey':$scope.fields.rkey.value
            }
         }
        ).then( 
                function(ret){
					$scope.setBusy(false);
					$scope.showLIST();
                },
                function(resp){ 
                    $scope.setBusy(false);
					$scope.InternalError(resp);
                }
        );
        
    }    
});