



import FloatFieldView from 'views/fields/float';
import Select from 'ui/select';


class CurrencyFieldView extends FloatFieldView {

    type = 'currency'

    editTemplate = 'fields/currency/edit'
    detailTemplate = 'fields/currency/detail'
    detailTemplate1 = 'fields/currency/detail-1'
    detailTemplate2 = 'fields/currency/detail-2'
    detailTemplate3 = 'fields/currency/detail-3'
    listTemplate = 'fields/currency/list'
    listTemplate1 = 'fields/currency/list-1'
    listTemplate2 = 'fields/currency/list-2'
    listTemplate3 = 'fields/currency/list-3'
    detailTemplateNoCurrency = 'fields/currency/detail-no-currency'

    maxDecimalPlaces = 3

    validations = [
        'required',
        'number',
        'range',
    ]

    
    data() {
        let currencyValue = this.model.get(this.currencyFieldName) ||
            this.getPreferences().get('defaultCurrency') ||
            this.getConfig().get('defaultCurrency');

        let multipleCurrencies = !this.isSingleCurrency || currencyValue !== this.defaultCurrency;

        return {
            ...super.data(),
            currencyFieldName: this.currencyFieldName,
            currencyValue: currencyValue,
            currencyOptions: this.currencyOptions,
            currencyList: this.currencyList,
            currencySymbol: this.getMetadata().get(['app', 'currency', 'symbolMap', currencyValue]) || '',
            multipleCurrencies: multipleCurrencies,
            defaultCurrency: this.defaultCurrency,
        };
    }

    
    setup() {
        super.setup();

        this.currencyFieldName = this.name + 'Currency';
        this.defaultCurrency = this.getConfig().get('defaultCurrency');
        this.currencyList = this.getConfig().get('currencyList') || [this.defaultCurrency];
        this.decimalPlaces = this.getConfig().get('currencyDecimalPlaces');

        if (this.params.onlyDefaultCurrency) {
            this.currencyList = [this.defaultCurrency];
        }

        this.isSingleCurrency = this.currencyList.length <= 1;

        let currencyValue = this.currencyValue = this.model.get(this.currencyFieldName) ||
            this.defaultCurrency;

        if (!~this.currencyList.indexOf(currencyValue)) {
            this.currencyList = Espo.Utils.clone(this.currencyList);
            this.currencyList.push(currencyValue);
        }
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

    getCurrencyFormat() {
        return this.getConfig().get('currencyFormat') || 1;
    }

    _getTemplateName() {
        if (this.mode === this.MODE_DETAIL || this.mode === this.MODE_LIST) {
            var prop;

            if (this.mode === this.MODE_LIST) {
                prop = 'listTemplate' + this.getCurrencyFormat().toString();
            }
            else {
                prop = 'detailTemplate' + this.getCurrencyFormat().toString();
            }

            if (this.options.hideCurrency) {
                prop = 'detailTemplateNoCurrency';
            }

            if (prop in this) {
                return this[prop];
            }
        }

        return super._getTemplateName();
    }

    formatNumber(value) {
        return this.formatNumberDetail(value);
    }

    formatNumberDetail(value) {
        if (value !== null) {
            let currencyDecimalPlaces = this.decimalPlaces;

            if (currencyDecimalPlaces === 0) {
                value = Math.round(value);
            }
            else if (currencyDecimalPlaces) {
                value = Math.round(
                    value * Math.pow(10, currencyDecimalPlaces)) / (Math.pow(10, currencyDecimalPlaces)
                );
            }
            else {
                value = Math.round(
                    value * Math.pow(10, this.maxDecimalPlaces)) / (Math.pow(10, this.maxDecimalPlaces)
                );
            }

            let parts = value.toString().split(".");

            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.thousandSeparator);

            if (currencyDecimalPlaces === 0) {
                return parts[0];
            }
            else if (currencyDecimalPlaces) {
                let decimalPartLength = 0;

                if (parts.length > 1) {
                    decimalPartLength = parts[1].length;
                } else {
                    parts[1] = '';
                }

                if (currencyDecimalPlaces && decimalPartLength < currencyDecimalPlaces) {
                    let limit = currencyDecimalPlaces - decimalPartLength;

                    for (let i = 0; i < limit; i++) {
                        parts[1] += '0';
                    }
                }
            }

            return parts.join(this.decimalMark);
        }

        return '';
    }

    parse(value) {
        value = (value !== '') ? value : null;

        if (value === null) {
            return null;
        }

        value = value.split(this.thousandSeparator).join('');
        value = value.split(this.decimalMark).join('.');

        if (!this.params.decimal) {
            value = parseFloat(value);
        }

        return value;
    }

    afterRender() {
        super.afterRender();

        if (this.mode === this.MODE_EDIT) {
            this.$currency = this.$el.find('[data-name="' + this.currencyFieldName + '"]');

            this.$currency.on('change', () => {
                this.model.set(this.currencyFieldName, this.$currency.val(), {ui: true});
            });

            Select.init(this.$currency);
        }
    }

    validateNumber() {
        if (!this.params.decimal) {
            return this.validateFloat();
        }

        let value = this.model.get(this.name);

        if (Number.isNaN(Number(value))) {
            let msg = this.translate('fieldShouldBeNumber', 'messages').replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }
    }

    fetch() {
        let value = this.$element.val().trim();

        value = this.parse(value);

        let data = {};

        let currencyValue = this.$currency.length ?
            this.$currency.val() :
            this.defaultCurrency;

        if (value === null) {
            currencyValue = null;
        }

        data[this.name] = value;
        data[this.currencyFieldName] = currencyValue;

        return data;
    }
}

export default CurrencyFieldView;
