app.controller('counters-ctrl-datas', function(Dialog, $window, $http, $scope)
{   
	$scope.recs = [];
	
	$scope.currentRecordId = null;	
    $scope.currentColumn = 'value';
	$scope.toggleSelect = function(rec, col) {
        if ($scope.busy) return;
        if (col!=null) $scope.currentColumn = col;        
        // new ?
        if (rec.id==-1) {
            $scope.editRecord(rec);
            return;
        }
        // update..
        if (($scope.currentRecordId == rec.id) && (col==null)) {
            $scope.currentRecordId = null;
        } else {
            $scope.currentRecordId = rec.id;
            if (col!=null) $scope.editRecord(rec);
        }
	}
	
    // delete one record
    $scope.deleteRecord = function(rec)
    {
        if ($scope.busy) return;
        if (rec.id==-1) return;
        var data = { };
        data.id = rec.id;
        data.cuid = $scope.currentCounter.cuid;  
        $http.delete($scope.SrvUrl("records"),{params:{'data': data}}).then(
            function(ret) {
                $scope.setBusy(false);		
                $scope.listRecords();
            },
            function(resp){ 
                $scope.InternalError(resp);
                $scope.setBusy(false);	
        }); 
    }
    
    $scope.editRecord = function(rec)
    {
        if ($scope.busy) return;
        var col = $scope.currentColumn;
        var data = {
            'class': 'newrecord_dlg',
            'active' : col,
            'lang' : $scope.lang,
            'ok' : $scope.S['DLG_OK'],
            'cancel' : $scope.S['DLG_CANCEL'],
            'delete' : $scope.S['DLG_DELETE'],
            'tdate': $scope.S['RECORD_DATE'],
            'tlatitude': $scope.S['RECORD_LATITUDE'],
            'tlongitude': $scope.S['RECORD_LONGITUDE'],
            'taltitude': $scope.S['RECORD_ALTITUDE'],
            'tauthor': $scope.S['RECORD_AUTHOR'],
            'ttext': $scope.S['RECORD_TEXT'],
            'tvalue': $scope.S['RECORD_VALUE']
        };
        
        data.id = rec.id;
        data.cuid = $scope.currentCounter.cuid;
        if (rec.id!=-1) {
            data.title = $scope.S['RECORD_UPDATE'],
            data.date = rec.date;
            data.lat = rec.lat;
            data.lng = rec.lng;
            data.alt = rec.alt;
            data.author = rec.author;
            data.text = rec.text;
            data.value = rec.value;
        } else {
            data.title = $scope.S['RECORD_NEW'];
        }
        
        Dialog.showDialog('newrecord',data).then(
            // ok
            function(data)
            {
                //console.log(action);
                switch(data.action)
                {
                    case 'delete': {  
                        
                        $scope.deleteRecord(data);
                        break;
                    }
                    case 'change': {  
                        if ($scope.busy) { $scope.showError($scope.S['ERROR']); return; }
                        $scope.setBusy(true);
                        
                        if (data.id==-1) {
                            // create a new record
                            $http.post($scope.SrvUrl("records"),{ 'data': data }).then(
                                function(ret) {
                                    console.log(ret.data.id);
                                    if(ret.data.id!=0) $scope.currentRecordId = ret.data.id;
                                    $scope.setBusy(false);		
                                    $scope.listRecords();
                                },
                                function(resp){ 
                                    $scope.InternalError(resp);
                                    $scope.setBusy(false);	
                            }); 
                        } else {
                            // update existing one                    
                            $http.put($scope.SrvUrl("records"),{ 'data': data }).then(
                                function(ret) {
                                    $scope.setBusy(false);		
                                    $scope.listRecords();
                                },
                                function(resp){ 
                                    $scope.InternalError(resp);
                                    $scope.setBusy(false);	
                            }); 
                        }
                        break;
                    }
                }
            }
        );
    }
    
    $scope.goToGoogleMap = function(rec) {
    }
    

	
    $scope.listRecords = function() {
		if ($scope.busy) return;
        $scope.setBusy(true);
        $http.get($scope.SrvUrl("records"),{params:{'data':{'cuid': $scope.currentCounter.cuid }}})   
          .then( 
            function(ret){
				$scope.recs = ret.data;
				for(i=$scope.recs.length;i<10;i++) $scope.recs.push({
					'id': -1,
					/*'date': 123456,
					'lat': 1.245698,
					'lng': 12.346,
					'alt': 123.45,
					'author': 'robert',
					'text': 'as',
					'value': 3.141516*/
				}); // add empty line
				$("#recordsTable").colResizable({liveDrag:true});
                $scope.setBusy(false);				
            },
            function(resp){ 
                $scope.InternalError(resp);
                $scope.setBusy(false);
            });  
    }
	
	$scope.listRecords();
});