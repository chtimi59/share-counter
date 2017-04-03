app.directive('scdAhrefConfirm', function(Dialog, $window)
{
	var ctx = {};
	ctx.restrict = 'A';	
	ctx.link = function (scope, element, attrs) { 
		element.bind('click', function (e) {
			Dialog.showDialog( 'question', {
				'class': 'warning_dlg',
				'title': attrs.title,
                'yes' : $scope.S['DLG_YES'],
                'no' : $scope.S['DLG_NO'],
				'msg': attrs.msg }
			).then(
				
				function() {
					//console.log('resolve');
					$window.location.href = attrs.href;
				},
				
				function() {
					//console.log('reject');
				}
			);
			
			scope.$digest();
			return false;
		});		
	};    
	return ctx;
});