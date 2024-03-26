

define('crm:views/account/fields/shipping-address', ['views/fields/address'], function (Dep) {

    return Dep.extend({

        copyFrom: 'billingAddress',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.attributePartList = this.getMetadata().get(['fields', 'address', 'actualFields']) || [];

            this.allAddressAttributeList = [];

            this.attributePartList.forEach(part => {
                this.allAddressAttributeList.push(this.copyFrom + Espo.Utils.upperCaseFirst(part));
                this.allAddressAttributeList.push(this.name + Espo.Utils.upperCaseFirst(part));
            });

            this.listenTo(this.model, 'change', () => {
                var isChanged = false;

                this.allAddressAttributeList.forEach(attribute => {
                    if (this.model.hasChanged(attribute)) {
                        isChanged = true;
                    }
                });

                if (isChanged) {
                    if (this.isEditMode() && this.isRendered() && this.$copyButton) {
                        if (this.toShowCopyButton()) {
                            this.$copyButton.removeClass('hidden');
                        } else {
                            this.$copyButton.addClass('hidden');
                        }
                    }
                }
            });
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode === 'edit') {
                var label = this.translate('Copy Billing', 'labels', 'Account');
                this.$copyButton = $('<button class="btn btn-default btn-sm">' + label + '</button>');

                this.$copyButton.on('click', () => {
                    this.copy(this.copyFrom);
                });

                if (!this.toShowCopyButton()) {
                    this.$copyButton.addClass('hidden');
                }

                this.$el.append(this.$copyButton);
            }
        },

        copy: function (fieldFrom) {
            Object.keys(this.getMetadata().get('fields.address.fields'))
                .forEach(attr => {
                    let destField = this.name + Espo.Utils.upperCaseFirst(attr);
                    let sourceField = fieldFrom + Espo.Utils.upperCaseFirst(attr);

                    this.model.set(destField, this.model.get(sourceField));
                });
        },

        toShowCopyButton: function () {
            var billingIsNotEmpty = false;
            var shippingIsNotEmpty = false;

            this.attributePartList.forEach(part => {
                let attribute1 = this.copyFrom + Espo.Utils.upperCaseFirst(part);

                if (this.model.get(attribute1)) {
                    billingIsNotEmpty = true;
                }

                let attribute2 = this.name + Espo.Utils.upperCaseFirst(part);

                if (this.model.get(attribute2)) {
                    shippingIsNotEmpty = true;
                }
            });

            return billingIsNotEmpty && !shippingIsNotEmpty;
        },
    });
});
