

import UrlFieldView from 'views/fields/url';
import Helper from 'helpers/misc/foreign-field';

class ForeignUrlFieldView extends UrlFieldView {

    type = 'foreign'
    readOnly = true

    setup() {
        super.setup();

        const helper = new Helper(this);

        const foreignParams = helper.getForeignParams();

        for (let param in foreignParams) {
            this.params[param] = foreignParams[param];
        }
    }
}

export default ForeignUrlFieldView;
