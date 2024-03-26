

import EnumFieldView from 'views/fields/enum';

class EnumIntFieldView extends EnumFieldView {

    type = 'enumInt'

    listTemplate = 'fields/enum/detail'
    detailTemplate = 'fields/enum/detail'
    editTemplate = 'fields/enum/edit'
    searchTemplate = 'fields/enum/search'

    validations = []

    fetch() {
        let value = parseInt(this.$element.val());
        let data = {};

        data[this.name] = value;

        return data;
    }

    parseItemForSearch(item) {
        return parseInt(item);
    }
}

export default EnumIntFieldView;
