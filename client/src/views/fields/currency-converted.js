

import CurrencyFieldView from 'views/fields/currency';

class CurrencyConvertedFieldView extends CurrencyFieldView {

    data() {
        let data = super.data();

        const currencyValue = this.getConfig().get('defaultCurrency');

        data.currencyValue = currencyValue;
        data.currencySymbol = this.getMetadata().get(['app', 'currency', 'symbolMap', currencyValue]) || '';

        return data;
    }
}

export default CurrencyConvertedFieldView;
