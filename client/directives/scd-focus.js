app.directive('scdFocus', function($timeout) {
  return {
    link: function(scope, element, attrs) {
      scope.$watch(attrs.scdFocus, function(value) {
        if(value === true) { 
            element[0].focus();
            scope[attrs.focusMe] = false;
        }
      });
    }
  };
});