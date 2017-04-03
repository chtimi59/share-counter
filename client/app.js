var app = angular.module('myApp', []);

app.run(['$templateCache', function ( $templateCache ) {
    $templateCache.removeAll(); }]);