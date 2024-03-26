

import VarcharFieldView from 'views/fields/varchar';

class NumberFieldView extends VarcharFieldView {

    type = 'number'

    validations = []

    inlineEditDisabled = true
    readOnly = true

    
    fetch() {
        return {};
    }
}

export default NumberFieldView;
