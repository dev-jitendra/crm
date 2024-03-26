

define('views/settings/fields/sms-provider', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        fetchEmptyValueAsNull: true,

        setupOptions: function () {
            this.params.options = Object.keys(
                this.getMetadata().get(['app', 'smsProviders']) || {}
            );

            this.params.options.unshift('');
        },
    });
});
