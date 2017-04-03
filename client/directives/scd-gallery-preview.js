"use strict";

app.directive('scdGalleryPreview', function () {
    return {
        restrict: 'E',        
        link: function ($scope, $element, $attrs) {  
			$scope.src = ($attrs.src=="")?null:'gallery/' + $attrs.src + '/preview.png';
			/*console.log($attrs.src);*/
            /*if ($attrs.elt=="") return;
			var src = 'gallery/' + $attrs.elt + '/preview.png';*/
			// template: '<div style="float:left; width:150px;"><img class="img-thumbnail" width="100%" src="gallery/highchart-line/preview.png"></div>
			/*var div = $('<div style="float:left; width:150px;">');
			
			$element.addClass('gallery_preview');*/
        },	
		controller: function($scope, $attrs) {
		},
		template: '<div ng-if="src!=null" style="float:left; width:150px;"><img width="100%" src="{{src}}"></div>'
    };
});
