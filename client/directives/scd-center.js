app.directive('scdCenter', function(Dialog, $window)
{
	var ctx = {};
	ctx.restrict = 'A';	
	ctx.link = function (scope, element, attrs)
	{ 
		var compute = function() {
			var size = attrs.scdCenter;
			var height = $window.innerHeight;
			var margin_top = (height-size)/2;
			if (margin_top<0) margin_top=0;
			element.css("margin-top", margin_top);
		}

        angular.element($window).bind('resize', function () { compute(); });
		compute();
	}
	return ctx;
});