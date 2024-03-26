

define('crm:views/campaign/tracking-url', ['view'], function (Dep) {

    return Dep.extend({

        template: 'crm:campaign/tracking-url',

        data: function () {
            return {
                message: this.options.message,
            };
        }

    });
});
