

import VarcharFieldView from 'views/fields/varchar';

class EmailAddressFieldView extends VarcharFieldView {

    editTemplate = 'fields/email-address/edit'

    validations = ['required', 'emailAddress']

    emailAddressRe = new RegExp(
        /^[-!#$%&'*+/=?^_`{|}~A-Za-z0-9]+(?:\.[-!#$%&'*+/=?^_`{|}~A-Za-z0-9]+)*/.source +
        /@([A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])?\.)+[A-Za-z0-9][A-Za-z0-9-]*[A-Za-z0-9]/.source
    )

    
    validateEmailAddress() {
        const value = this.model.get(this.name);

        if (!value) {
            return false;
        }

        if (value !== '' && !this.emailAddressRe.test(value)) {
            const msg = this.translate('fieldShouldBeEmail', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }

        return false;
    }
}

export default EmailAddressFieldView;
