

import FloatFieldView from 'views/fields/float';
import Helper from 'helpers/misc/foreign-field';

class ForeignFloatFieldView extends FloatFieldView {

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

export default ForeignFloatFieldView;
