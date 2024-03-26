

import RangeIntFieldView from 'views/fields/range-int';
import FloatFieldView from 'views/fields/float';

class RangeFloatFieldView extends RangeIntFieldView {

    type = 'rangeFloat'

    validations = ['required', 'float', 'range', 'order']
    decimalPlacesRawValue = 10

    setupAutoNumericOptions() {
        this.autoNumericOptions = {
            digitGroupSeparator: this.thousandSeparator || '',
            decimalCharacter: this.decimalMark,
            modifyValueOnWheel: false,
            selectOnFocus: false,
            decimalPlaces: this.decimalPlacesRawValue,
            decimalPlacesRawValue: this.decimalPlacesRawValue,
            allowDecimalPadding: false,
            showWarnings: false,
            formulaMode: true,
        };
    }

    
    validateFloat() {
        const validate = (name) => {
            if (isNaN(this.model.get(name))) {
                let msg = this.translate('fieldShouldBeFloat', 'messages')
                    .replace('{field}', this.getLabelText());

                this.showValidationMessage(msg, '[data-name="' + name + '"]');

                return true;
            }
        };

        let result = false;

        result = validate(this.fromField) || result;
        result = validate(this.toField) || result;

        return result;
    }

    parse(value) {
        return FloatFieldView.prototype.parse.call(this, value);
    }

    formatNumber(value) {
        return FloatFieldView.prototype.formatNumberDetail.call(this, value);
    }
}

export default RangeFloatFieldView;

