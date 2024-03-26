

import BaseFieldView from 'views/fields/base';

class PasswordFieldView extends BaseFieldView {

    type = 'password'

    detailTemplate = 'fields/password/detail'
    editTemplate = 'fields/password/edit'

    validations = ['required', 'confirm']

    events = {
        
        'click [data-action="change"]': function () {
            this.changePassword();
        },
    }

    changePassword() {
        this.$el.find('[data-action="change"]').addClass('hidden');
        this.$element.removeClass('hidden');

        this.changing = true;
    }

    
    data() {
        return {
            isNew: this.model.isNew(),
            ...super.data(),
        }
    }

    
    validateConfirm() {
        if (!this.model.has(this.name + 'Confirm')) {
            return;
        }

        if (this.model.get(this.name) !== this.model.get(this.name + 'Confirm')) {
            let msg = this.translate('fieldBadPasswordConfirm', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }
    }

    afterRender() {
        super.afterRender();

        this.changing = false;

        if (this.params.readyToChange) {
            this.changePassword();
        }
    }

    fetch() {
        if (!this.model.isNew() && !this.changing) {
            return {};
        }

        return super.fetch();
    }
}

export default PasswordFieldView;
