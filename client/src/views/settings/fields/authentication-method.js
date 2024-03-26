

define('views/settings/fields/authentication-method', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = [];

            let defs = this.getMetadata().get(['authenticationMethods']) || {};

            for (let method in defs) {
                if (defs[method].settings && defs[method].settings.isAvailable) {
                    this.params.options.push(method);
                }
            }
        },
    });
});
