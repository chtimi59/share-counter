"use strict";

app.service('Dialog', function ($q) {
    var dialogs = {};
    return {
        registerDialog: function (id, element, scope) {
            dialogs[id] = {
                element:element,
                scope: scope
            };
        },
       
        showDialog: function (id, data) {			
            var dlg = dialogs[id];
            var deferred = $q.defer();

            function resolve() {
                dlg.scope.isVisible = false;
                deferred.resolve.apply(deferred, arguments);
            }
            function reject() {
                dlg.scope.isVisible = false;
                deferred.reject.apply(deferred, arguments);
            }
            if (dlg) {
                dlg.scope.isVisible = true;
                dlg.scope.resolve = resolve;
                dlg.scope.reject = reject;
                dlg.scope.data = data;
            } else {
                throw (new Error('Invalid dialog ' + id));
            }
            return deferred.promise;
        }
    };
});

