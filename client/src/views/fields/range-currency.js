

import RangeFloatFieldView from 'views/fields/range-float';
import CurrencyFieldView from 'views/fields/currency';
import Select from 'ui/select';

class RangeCurrencyFieldView extends RangeFloatFieldView {

    type = 'rangeCurrency'

    editTemplate = 'fields/range-currency/edit'

    data() {
        return {
            currencyField: this.currencyField,
            currencyValue: this.model.get(this.fromCurrencyField) ||
                this.getPreferences().get('defaultCurrency') ||
                this.getConfig().get('defaultCurrency'),
            currencyList: this.currencyList,
            ...super.data(),
        }
    }

    setup() {
        super.setup();

        const ucName = Espo.Utils.upperCaseFirst(this.name);

        this.fromCurrencyField = 'from' + ucName + 'Currency';
        this.toCurrencyField = 'to' + ucName + 'Currency';

        this.currencyField = this.name + 'Currency';
        this.currencyList = this.getConfig().get('currencyList') || ['USD'];
        this.decimalPlaces = this.getConfig().get('currencyDecimalPlaces');
    }

    setupAutoNumericOptions() {
        this.autoNumericOptions = {
            digitGroupSeparator: this.thousandSeparator || '',
            decimalCharacter: this.decimalMark,
            modifyValueOnWheel: false,
            selectOnFocus: false,
            decimalPlaces: this.decimalPlaces,
            allowDecimalPadding: true,
            showWarnings: false,
            formulaMode: true,
        };

        if (this.decimalPlaces === null) {
            this.autoNumericOptions.decimalPlaces = this.decimalPlacesRawValue;
            this.autoNumericOptions.decimalPlacesRawValue = this.decimalPlacesRawValue;
            this.autoNumericOptions.allowDecimalPadding = false;
        }
    }

    afterRender() {
        super.afterRender();

        if (this.mode === this.MODE_EDIT) {
            this.$currency = this.$el.find('[data-name="' + this.currencyField + '"]');

            Select.init(this.$currency);
        }
    }

    formatNumber(value) {
        return CurrencyFieldView.prototype.formatNumberDetail.call(this, value);
    }

    getValueForDisplay() {
        let fromValue = this.model.get(this.fromField);
        let toValue = this.model.get(this.toField);

        fromValue = isNaN(fromValue) ? null : fromValue;
        toValue = isNaN(toValue) ? null : toValue;

        let currencyValue = this.model.get(this.fromCurrencyField) ||
            this.model.get(this.toCurrencyField);

        let symbol = this.getMetadata().get(['app', 'currency', 'symbolMap', currencyValue]) || currencyValue;

        if (fromValue !== null && toValue !== null) {
            return this.formatNumber(fromValue) + ' &#8211 ' +
                this.formatNumber(toValue) + ' ' + symbol + '';
        }

        if (fromValue) {
            return '&#62;&#61; ' + this.formatNumber(fromValue) + ' ' + symbol+'';
        }

        if (toValue) {
            return '&#60;&#61; ' + this.formatNumber(toValue) + ' ' + symbol+'';
        }

        return this.translate('None');
    }

    fetch() {
        const data = super.fetch();

        let currencyValue = this.$currency.val();

        if (data[this.fromField] !== null) {
            data[this.fromCurrencyField] = currencyValue;
        }
        else {
            data[this.fromCurrencyField] = null;
        }

        if (data[this.toField] !== null) {
            data[this.toCurrencyField] = currencyValue;
        }
        else {
            data[this.toCurrencyField] = null;
        }

        return data;
    }
}


export default RangeCurrencyFieldView;
