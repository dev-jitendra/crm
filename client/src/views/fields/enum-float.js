

import EnumIntFieldView from 'views/fields/enum-int';

class EnumFloatFieldView extends EnumIntFieldView {

    type = 'enumFloat'

    fetch() {
        let value = parseFloat(this.$element.val());
        let data = {};

        data[this.name] = value;

        return data;
    }

    parseItemForSearch(item) {
        return parseFloat(item);
    }
}

export default EnumFloatFieldView;
