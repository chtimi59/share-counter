app.directive('scdAction', function(Dialog, $window)
{
	var ctx = {};
	ctx.restrict = 'A';	
	ctx.link = function (scope, element, attrs)
	{ 
		element.bind('click', function (e) {
			var url = "index.php";
			var arg = false;
            // is destination is the same, just a basic reload
            if (scope.action==attrs.scdAction) {
                $window.location.reload()
                return false;
            }
			// set action
			if (attrs.scdAction!=null) { url+=(arg)?"&":"?"; url+="action="+attrs.scdAction; arg=true; }
			// set optional extra arguments
			if (attrs.arg!=null) { url+=(arg)?"&":"?"; url+=attrs.arg; arg=true; }
			// set previous Argument
            url+=(arg)?"&":"?"; url+=scope.preArg;            
			$window.location.href = url;
			return false;
		});		
		
		// necessary to avoid infinite compile loop
		element.removeAttr('scd-action'); 
		// get a mouse pointer
		element.attr('href', '#');
	}    
	return ctx;
});