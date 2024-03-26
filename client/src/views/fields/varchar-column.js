

import VarcharFieldView from 'views/fields/varchar';

class VarcharColumnFieldView extends VarcharFieldView {

    searchTypeList = [
        'startsWith',
        'contains',
        'equals',
        'endsWith',
        'like',
        'isEmpty',
        'isNotEmpty',
    ]

    fetchSearch() {
        const type = this.fetchSearchType() || 'startsWith';

        if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
            if (type === 'isEmpty') {
                return {
                    typeFront: type,
                    where: {
                        type: 'or',
                        value: [
                            {
                                type: 'columnIsNull',
                                field: this.name,
                            },
                            {
                                type: 'columnEquals',
                                field: this.name,
                                value: '',
                            },
                        ],
                    },
                };
            }

            return  {
                typeFront: type,
                where: {
                    type: 'and',
                    value: [
                        {
                            type: 'columnNotEquals',
                            field: this.name,
                            value: '',
                        },
                        {
                            type: 'columnIsNotNull',
                            field: this.name,
                            value: null,
                        },
                    ],
                },
            };
        }

        let value = this.$element.val().toString().trim();

        value = value.trim();

        if (value) {
            return {
                value: value,
                type: 'column' . Espo.Utils.upperCaseFirst(type),
                data: {
                    type: type,
                    value: value,
                },
            };
        }

        return null;
    }
}

export default VarcharColumnFieldView;

