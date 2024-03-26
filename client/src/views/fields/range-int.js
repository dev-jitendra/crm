

import BaseFieldView from 'views/fields/base';
import IntFieldView from 'views/fields/int';
import AutoNumeric from 'autonumeric';

class RangeIntFieldView extends BaseFieldView {

    type = 'rangeInt'

    listTemplate = 'fields/range-int/detail'
    detailTemplate = 'fields/range-int/detail'
    editTemplate = 'fields/range-int/edit'

    validations = ['required', 'int', 'range', 'order']

    
    data() {
        const data = super.data();

        data.ucName = Espo.Utils.upperCaseFirst(this.name);
        data.fromValue = this.model.get(this.fromField);
        data.toValue = this.model.get(this.toField);

        
        return data;
    }

    init() {
        const ucName = Espo.Utils.upperCaseFirst(this.options.defs.name);

        this.fromField = 'from' + ucName;
        this.toField = 'to' + ucName;

        super.init();
    }

    getValueForDisplay() {
        let fromValue = this.model.get(this.fromField);
        let toValue = this.model.get(this.toField);

        fromValue = isNaN(fromValue) ? null : fromValue;
        toValue = isNaN(toValue) ? null : toValue;

        if (fromValue !== null && toValue !== null) {
            return this.formatNumber(fromValue) + ' &#8211 ' + this.formatNumber(toValue);
        }
        else if (fromValue) {
            return '&#62;&#61; ' + this.formatNumber(fromValue);
        }
        else if (toValue) {
            return '&#60;&#61; ' + this.formatNumber(toValue);
        }

        return this.translate('None');
    }

    setup() {
        if (this.getPreferences().has('decimalMark')) {
            this.decimalMark = this.getPreferences().get('decimalMark');
        }
        else {
            if (this.getConfig().has('decimalMark')) {
                this.decimalMark = this.getConfig().get('decimalMark');
            }
        }

        if (this.getPreferences().has('thousandSeparator')) {
            this.thousandSeparator = this.getPreferences().get('thousandSeparator');
        }
        else {
            if (this.getConfig().has('thousandSeparator')) {
                this.thousandSeparator = this.getConfig().get('thousandSeparator');
            }
        }
    }

    setupFinal() {
        super.setupFinal();

        this.setupAutoNumericOptions();
    }

    
    setupAutoNumericOptions() {
        let separator = (!this.disableFormatting ? this.thousandSeparator : null) || '';
        let decimalCharacter = '.';

        if (separator === '.') {
            decimalCharacter = ',';
        }

        this.autoNumericOptions = {
            digitGroupSeparator: separator,
            decimalCharacter: decimalCharacter,
            modifyValueOnWheel: false,
            decimalPlaces: 0,
            selectOnFocus: false,
            formulaMode: true,
        };
    }

    afterRender() {
        super.afterRender();

        if (this.mode === this.MODE_EDIT) {
            this.$from = this.$el.find('[data-name="' + this.fromField + '"]');
            this.$to = this.$el.find('[data-name="' + this.toField + '"]');

            this.$from.on('change', () => {
                this.trigger('change');
            });

            this.$to.on('change', () => {
                this.trigger('change');
            });

            if (this.autoNumericOptions) {
                
                this.autoNumericInstance1 = new AutoNumeric(this.$from.get(0), this.autoNumericOptions);
                
                this.autoNumericInstance2 = new AutoNumeric(this.$to.get(0), this.autoNumericOptions);
            }
        }
    }

    validateRequired() {
        const validate = (name) => {
            if (this.model.isRequired(name)) {
                if (this.model.get(name) === null) {
                    var msg = this.translate('fieldIsRequired', 'messages')
                        .replace('{field}', this.getLabelText());

                    this.showValidationMessage(msg, '[data-name="' + name + '"]');

                    return true;
                }
            }
        };

        let result = false;

        result = validate(this.fromField) || result;
        result = validate(this.toField) || result;

        return result;
    }

    
    validateInt() {
        const validate = (name) => {
            if (isNaN(this.model.get(name))) {
                var msg = this.translate('fieldShouldBeInt', 'messages')
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

    
    validateRange() {
        const validate = (name) => {
            var value = this.model.get(name);

            if (value === null) {
                return false;
            }

            var minValue = this.model.getFieldParam(name, 'min');
            var maxValue = this.model.getFieldParam(name, 'max');

            if (minValue !== null && maxValue !== null) {
                if (value < minValue || value > maxValue) {
                    let msg = this.translate('fieldShouldBeBetween', 'messages')
                        .replace('{field}', this.translate(name, 'fields', this.entityType))
                        .replace('{min}', minValue)
                        .replace('{max}', maxValue);

                    this.showValidationMessage(msg, '[data-name="' + name + '"]');

                    return true;
                }
            } else {
                if (minValue !== null) {
                    if (value < minValue) {
                        let msg = this.translate('fieldShouldBeLess', 'messages')
                            .replace('{field}', this.translate(name, 'fields', this.entityType))
                            .replace('{value}', minValue);

                        this.showValidationMessage(msg, '[data-name="' + name + '"]');

                        return true;
                    }
                } else if (maxValue !== null) {
                    if (value > maxValue) {
                        let msg = this.translate('fieldShouldBeGreater', 'messages')
                            .replace('{field}', this.translate(name, 'fields', this.entityType))
                            .replace('{value}', maxValue);

                        this.showValidationMessage(msg, '[data-name="' + name + '"]');

                        return true;
                    }
                }
            }
        };

        let result = false;

        result = validate(this.fromField) || result;
        result = validate(this.toField) || result;

        return result;
    }

    
    validateOrder() {
        let fromValue = this.model.get(this.fromField);
        let toValue = this.model.get(this.toField);

        if (fromValue !== null && toValue !== null) {
            if (fromValue > toValue) {
                let msg = this.translate('fieldShouldBeGreater', 'messages')
                    .replace('{field}', this.translate(this.toField, 'fields', this.entityType))
                    .replace('{value}', this.translate(this.fromField, 'fields', this.entityType));

                this.showValidationMessage(msg, '[data-name="'+this.fromField+'"]');

                return true;
            }
        }
    }

    isRequired() {
        return this.model.getFieldParam(this.fromField, 'required') ||
            this.model.getFieldParam(this.toField, 'required');
    }

    parse(value) {
        return IntFieldView.prototype.parse.call(this, value);
    }

    formatNumber(value) {
        return IntFieldView.prototype.formatNumberDetail.call(this, value);
    }

    fetch() {
        let data = {};

        data[this.fromField] = this.parse(this.$from.val().trim());
        data[this.toField] = this.parse(this.$to.val().trim());

        return data;
    }
}

export default RangeIntFieldView;
