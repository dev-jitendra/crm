

import EnumFieldView from 'views/fields/enum';

export default class extends EnumFieldView {

    searchTypeList = ['anyOf', 'noneOf']

    fetchSearch() {
        let data = super.fetchSearch();

        if (
            data &&
            data.data.type === 'noneOf' &&
            data.value &&
            data.value.length > 1
        ) {
            data.value = [data.value[0]];
        }

        return data;
    }
}
