

import IntFieldView from 'views/fields/int';
import Helper from 'helpers/misc/foreign-field';

class ForeignIntFieldView extends IntFieldView {

    type = 'foreign'

    setup() {
        super.setup();

        const helper = new Helper(this);

        const foreignParams = helper.getForeignParams();

        for (let param in foreignParams) {
            this.params[param] = foreignParams[param];
        }

        this.disableFormatting = foreignParams.disableFormatting;
    }
}

export default ForeignIntFieldView;

