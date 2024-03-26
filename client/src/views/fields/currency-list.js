

import EnumFieldView from 'views/fields/enum';

class CurrencyListFieldView extends EnumFieldView {

    setupOptions() {
        this.params.options = [];

        (this.getConfig().get('currencyList') || []).forEach(item => {
            this.params.options.push(item);
        });
    }
}

export default CurrencyListFieldView;
