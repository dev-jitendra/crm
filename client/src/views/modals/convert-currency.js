

import MassConvertCurrencyModalView from 'views/modals/mass-convert-currency';

class ConvertCurrencyModalView extends MassConvertCurrencyModalView {

    setup() {
        super.setup();

        this.headerText = this.translate('convertCurrency', 'massActions');
    }

    actionConvert() {
        this.disableButton('convert');

        this.getFieldView('currency').fetchToModel();
        this.getFieldView('currencyRates').fetchToModel();

        const currency = this.model.get('currency');
        const currencyRates = this.model.get('currencyRates');

        Espo.Ajax
            .postRequest('Action', {
                entityType: this.options.entityType,
                action: 'convertCurrency',
                id: this.options.model.id,
                data: {
                    targetCurrency: currency,
                    rates: currencyRates,
                    fieldList: this.options.fieldList || null,
                },
            })
            .then(attributes => {
                this.trigger('after:update', attributes);

                this.close();
            })
            .catch(() => {
                this.enableButton('convert');
            });
    }
}

export default ConvertCurrencyModalView;
