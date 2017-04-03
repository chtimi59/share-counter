"use strict";

app.directive('scdDialog', function (Dialog) {
    return {
        restrict: 'E',
        transclude: true,
        scope: {
            scdDialog: '=id'
        },
        link: function ($scope, $element, $attrs) {            
			$scope.S = S;
            $scope.bgStyle = {
                position: 'fixed',
                left: 0,
                right: 0,
                top: 0,
                bottom: 0,
            };
            $scope.fgStyle = {
                'margin-left': 'auto',
                'margin-right': 'auto',
            },
            Dialog.registerDialog($scope.scdDialog, $element, $scope);
        },
        template: '<div class="dialog-bg" ng-class="data.class" ng-if="isVisible" ng-style="bgStyle">' +
            '<div class="dialog-box" ng-transclude ng-style="fgStyle"></div>' +
            '</div>'
    };
});

app.directive('scdDialogTitlebar', function () {
    return {
        restrict: 'E',
        transclude: true,
        link: function ($scope, $element, $attrs) {
            $scope.btnStyle = {
                float: 'right'
            };
            $scope.barStyle = {
                width: '100%',
                'text-align': 'center'
            };
        },
    template: '<div class="dialog-title" ng-style="barStyle">' +
        '<span ng-transclude></span>' +
        '<div ng-style="btnStyle" class="cross">' +
            '<a href="#" ng-click="reject()"><i class="fa fa-times"></i></a>' +
        '</div>' +
        '</div>'
    };
});
