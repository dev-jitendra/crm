

define('views/admin/field-manager/fields/options/default', ['views/fields/enum'], function (Dep) {

    
    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.validations.push('listed');

            this.updateAvailableOptions();

            this.listenTo(this.model, 'change', () => {
                if (
                    !this.model.hasChanged('options') &&
                    !this.model.hasChanged('optionsReference')
                ) {
                    return;
                }

                this.updateAvailableOptions();
            });
        },

        updateAvailableOptions: function () {
            this.setOptionList(this.getAvailableOptions());
        },

        getAvailableOptions: function () {
            const optionsReference = this.model.get('optionsReference');

            if (optionsReference) {
                const [entityType, field] = optionsReference.split('.');

                const options = this.getMetadata()
                    .get(`entityDefs.${entityType}.fields.${field}.options`) || [''];

                return Espo.Utils.clone(options);
            }

            return this.model.get('options') || [''];
        },

        validateListed: function () {
            const value = this.model.get(this.name) ?? '';

            if (!this.params.options) {
                return false;
            }

            const options = this.getAvailableOptions();

            if (options.indexOf(value) === -1) {
                const msg = this.translate('fieldInvalid', 'messages')
                    .replace('{field}', this.getLabelText());

                this.showValidationMessage(msg);

                return true;
            }

            return false;
        },
    });
});
