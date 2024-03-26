

define('views/settings/fields/auth-two-fa-method-list', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = [];

            let defs = this.getMetadata().get(['app', 'authentication2FAMethods']) || {};

            for (let method in defs) {
                if (defs[method].settings && defs[method].settings.isAvailable) {
                    this.params.options.push(method);
                }
            }
        },
    });
});
