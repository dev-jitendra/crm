

define('views/settings/fields/email-address-lookup-entity-type-list',
['views/fields/entity-type-list'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            Dep.prototype.setupOptions.call(this);

            this.params.options = this.params.options.filter(scope => {
                if (this.getMetadata().get(['scopes', scope, 'disabled'])) {
                    return;
                }

                if (!this.getMetadata().get(['scopes', scope, 'object'])) {
                    return;
                }

                if (~['User', 'Contact', 'Lead', 'Account'].indexOf(scope)) {
                    return true;
                }

                var type = this.getMetadata().get(['scopes', scope, 'type']);

                if (type === 'Company' || type === 'Person') {
                    return true;
                }
            })
        },
    });
});
