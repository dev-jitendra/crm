

import IntFieldView from 'views/fields/int';

class AutoincrementFieldView extends IntFieldView {

    type = 'autoincrement'

    validations = []

    inlineEditDisabled = true
    readOnly = true
    disableFormatting = true

    parse(value) {
        value = (value !== '') ? value : null;

        if (value !== null) {
            value = value.indexOf('.') !== -1 || value.indexOf(',') !== -1 ?
                NaN :
                parseInt(value);
        }

        return value;
    }

    fetch() {
        return {};
    }
}

export default AutoincrementFieldView;
