app.controller('main-ctrl', function(Dialog, $q, $window, $document,$scope, $http, $location)
{
    /* language */
    lang = getCookie('lang');
    switch (lang) {
        case 'en': $scope.lang = lang; break;
        case 'fr': $scope.lang = lang; break;
        default:   $scope.lang = 'en'; break;
    }
    $scope.S = S[$scope.lang];
	
    /* what what the previous url? */
    $scope.previousUrl = "";
    /* Absolute base Url (like "http://localhost/share-counter/") */
    $scope.baseUrl = "";
    /* set if a user is logged */
    $scope.user = null;
    /* Am I in a private aera? (logging needed) */
    $scope.isPrivatePage = false;

    /* Webservice Url */
    $scope.SrvUrl = function (name) {
        return $scope.baseUrl+"server/"+name+".api.php";
    }
    
    /* Crash! */
    $scope.InternalError = function (resp) {
        console.error(resp);
		console.error(resp.data);
        var idx = 'ERROR';
        if (resp.data.idx && resp.data.idx!='') idx = resp.data.idx;
        $scope.showError($scope.S[idx]);
    }    
    
    /* global strings dictionary binding */
    $scope.setLan = function(lang) {
		$scope.lang = lang;
        $scope.S = S[lang];
        setCookie('lang', lang);
		console.log(lang);
        if($scope.user) {
            $http.post($scope.SrvUrl("log"), {'action':'update', 'data':{'lang':lang}})
               .then( function(ret){ }, function(resp){ $scope.InternalError(resp); } );
        }
    }
    
    /* Popup an error message */
    $scope.popupError = null;
    $scope.showError = function(msg) {
        $scope.popupError = msg;
        $( "#popupError" ).fadeIn().delay(2000).fadeOut();
    }
    $scope.hideError = function() {
        $scope.popupError = "";
        $( "#popupError" ).fadeOut();
    }
    
    /* Popup an informative message */
    $scope.popupInfo = null;
    $scope.showInfo = function(msg) {
        $scope.popupInfo = msg;
        $( "#popupInfo" ).fadeIn().delay(2000).fadeOut();
    }
    $scope.hideInfo = function() {
        $scope.popupInfo = "";
        $( "#popupInfo" ).fadeOut();
    }
   
    /* Clipboard message */
	ZeroClipboard.on( "aftercopy", function( event ) {
	   $scope.showInfo($scope.S['COPYTOCLIP']);
	   $scope.$digest();
	});

    /* Basic MessageBox */
    $scope.showDialog = function(msg,cb) {
        Dialog.showDialog( 'message', {'msg': msg, 'ok': $scope.S['DLG_OK']})
            .then(function() { cb() });
    }
    
    /* Login */
    $scope.login = function(data) {
		var deferred = $q.defer();
        if (data == null)    { deferred.reject(); return deferred.promise; }
        if (data.name == "") { deferred.reject(); return deferred.promise; }
        if (data.pw == null) { deferred.reject(); return deferred.promise; }
        if (data.pw == "")   { deferred.reject(); return deferred.promise; }
        
        $http.post($scope.SrvUrl("log"),{'action':'login', 'data':data})
          .then(
            function(resp)  { 
                // 200 OK (go back where we comes from)
				deferred.resolve();
                $window.location.href = $scope.previousUrl;
            }
            ,function(resp) { 
                switch (resp.status) {
                    case 403: // forbidden
                        $scope.showError($scope.S['LOGIN_ERRORLOGIN']);
                        break;
                    default: // various error
                        $scope.InternalError(resp);
                        break;
                }
				deferred.reject();
            }
        );
		
		return deferred.promise;
    }
                
    /* Logout */
    $scope.logout = function() {
        $http.post($scope.SrvUrl("log"), { 'action' : 'logout' })
          .then(
            function(resp)  { 
                // 200 OK
                if ($scope.isPrivatePage) {
                    $window.location.href = "index.php"; 
                } else {
                    $scope.user = null;
                }
            }
            ,function(resp){ $scope.InternalError(resp); }
        );
    }
    
});