

define('views/user/fields/user-name', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.validations.push('userName');
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            let userNameRegularExpression = this.getUserNameRegularExpression();

            if (this.isEditMode()) {
                this.$element.on('change', () => {
                    let value = this.$element.val();
                    let re = new RegExp(userNameRegularExpression, 'gi');

                    value = value
                        .replace(re, '')
                        .replace(/[\s]/g, '_')
                        .toLowerCase();

                    this.$element.val(value);
                    this.trigger('change');
                });
            }
        },

        getUserNameRegularExpression: function () {
            return this.getConfig().get('userNameRegularExpression') || '[^a-z0-9\-@_\.\s]';
        },

        validateUserName: function () {
            let value = this.model.get(this.name);

            if (!value) {
                return;
            }

            let userNameRegularExpression = this.getUserNameRegularExpression();

            let re = new RegExp(userNameRegularExpression, 'gi');

            if (!re.test(value)) {
                return;
            }

            let msg = this.translate('fieldInvalid', 'messages').replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        },
    });
});
