

import EnumFieldView from 'views/fields/enum';

class EnumColumnFieldView extends EnumFieldView {

    searchTypeList = ['anyOf', 'noneOf']

    fetchSearch() {
        let type = this.fetchSearchType();

        let list = this.$element.val().split(':,:');

        if (list.length === 1 && list[0] === '') {
            list = [];
        }

        list.forEach((item, i) => {
            list[i] = this.parseItemForSearch(item);
        });

        if (type === 'anyOf') {
            if (list.length === 0) {
                return {
                    data: {
                        type: 'anyOf',
                        valueList: list,
                    },
                };
            }

            return {
                type: 'columnIn',
                value: list,
                data: {
                    type: 'anyOf',
                    valueList: list,
                },
            };
        }
        else if (type === 'noneOf') {
            if (list.length === 0) {
                return {
                    data: {
                        type: 'noneOf',
                        valueList: list,
                    },
                };
            }

            return {
                type: 'or',
                value: [
                    {
                        type: 'columnIsNull',
                        attribute: this.name,
                    },
                    {
                        type: 'columnNotIn',
                        value: list,
                        attribute: this.name,
                    }
                ],
                data: {
                    type: 'noneOf',
                    valueList: list,
                },
            };
        }

        return null;
    }
}

export default EnumColumnFieldView;
