

import TextFieldView from 'views/fields/text';
import Helper from 'helpers/misc/foreign-field';

class ForeignTextFieldView extends TextFieldView {

    type = 'foreign'

    setup() {
        super.setup();

        const helper = new Helper(this);

        const foreignParams = helper.getForeignParams();

        for (let param in foreignParams) {
            this.params[param] = foreignParams[param];
        }
    }
}

export default ForeignTextFieldView;
