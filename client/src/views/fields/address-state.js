

import VarcharFieldView from 'views/fields/varchar';

class AddressStateFieldView extends VarcharFieldView {

    setupOptions() {
        let stateList = this.getConfig().get('addressStateList') || [];

        if (stateList.length) {
            this.params.options = Espo.Utils.clone(stateList);
        }
    }
}

export default AddressStateFieldView;
