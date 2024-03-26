

import EmailFieldView from 'views/fields/email';

class ForeignEmailFieldView extends EmailFieldView {

    type = 'foreign'
    readOnly = true
}

export default ForeignEmailFieldView;
