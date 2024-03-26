

import VarcharFieldView from 'views/fields/varchar';

class AddressCityFieldView extends VarcharFieldView {

    setupOptions() {
        let cityList = this.getConfig().get('addressCityList') || [];

        if (cityList.length) {
            this.params.options = Espo.Utils.clone(cityList);
        }
    }
}

export default AddressCityFieldView;
