

define('crm:views/contact/fields/title', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(
                this.getMetadata().get('entityDefs.Account.fields.contactRole.options') || []
            );
        },
    });
});
