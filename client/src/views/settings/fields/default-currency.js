

define('views/settings/fields/default-currency', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.validations.push('existing');
        },

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(this.getConfig().get('currencyList') || []);
        },

        validateExisting: function () {
            var currencyList = this.model.get('currencyList');

            if (!currencyList) {
                return;
            }

            var value = this.model.get(this.name);

            if (~currencyList.indexOf(value)) {
                return;
            }

            var msg = this.translate('fieldInvalid', 'messages').replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        },
    });
});
