

import BaseFieldView from 'views/fields/base';

class JsonObjectFieldView extends BaseFieldView {

    type = 'jsonObject'

    listTemplate = 'fields/json-object/detail'
    detailTemplate = 'fields/json-object/detail'

    data() {
        const data = super.data();

        data.valueIsSet = this.model.has(this.name);
        data.isNotEmpty = !!this.model.get(this.name);

        return data;
    }

    getValueForDisplay() {
        const value = this.model.get(this.name);

        if (!value) {
            return null;
        }

        return JSON.stringify(value, null, 2)
            .replace(/(\r\n|\n|\r)/gm, '<br>').replace(/\s/g, '&nbsp;');
    }
}

export default JsonObjectFieldView;

