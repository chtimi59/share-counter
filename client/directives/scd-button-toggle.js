app.directive('scdButtonToggle', function($parse, $compile)
{
	var ctx = {};
	ctx.restrict = 'A';	
	ctx.link = function (scope, element, attrs) { };
    ctx.compile = function(el, attrs)
	{
		el.removeAttr('scd-button-toggle'); // necessary to avoid infinite compile loop
		el.attr("type", "button");		
		el.addClass("btn");	
		el.attr("ng-class", "{'active': "+attrs.field + "=='"+attrs.value+"'}");		
		el.attr("ng-click", attrs.field + "='"+attrs.value+"'");
		//var exp = $parse(attrs.field + "='" + attrs.value+"'");
		var fn = $compile(el);		
		
		return function(scope, elm) { 
			/*elm.bind('click', function() {
				console.log(exp);
				exp(scope);
			})*/;
			fn(scope);
		};
    }
	
	return ctx;
});