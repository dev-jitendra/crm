

import UrlMultipleFieldView from 'views/fields/url-multiple';
import Helper from 'helpers/misc/foreign-field';

class ForeignUrlMultipleFieldView extends UrlMultipleFieldView {

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

export default ForeignUrlMultipleFieldView;
