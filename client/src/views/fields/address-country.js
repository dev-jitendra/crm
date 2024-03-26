

import VarcharFieldView from 'views/fields/varchar';

class AddressCountryFieldView extends VarcharFieldView {

    setupOptions() {
        let countryList = this.getConfig().get('addressCountryList') || [];

        if (countryList.length) {
            this.params.options = Espo.Utils.clone(countryList);
        }
    }
}

export default AddressCountryFieldView;
