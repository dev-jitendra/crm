

import BoolFieldView from 'views/fields/bool';
import Helper from 'helpers/misc/foreign-field';

class ForeignBoolFieldView extends BoolFieldView {

    type = 'foreign'

    setup() {
        super.setup();

        let helper = new Helper(this);

        let foreignParams = helper.getForeignParams();

        for (let param in foreignParams) {
            this.params[param] = foreignParams[param];
        }
    }
}

export default ForeignBoolFieldView;
