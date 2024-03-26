

import DateFieldView from 'views/fields/date';
import Helper from 'helpers/misc/foreign-field';

class ForeignDateFieldView extends DateFieldView {

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

export default ForeignDateFieldView;
