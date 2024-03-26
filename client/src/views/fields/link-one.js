

import LinkFieldView from 'views/fields/link';

class LinkOneFieldView extends LinkFieldView {

    searchTypeList = ['is', 'isEmpty', 'isNotEmpty', 'isOneOf']

    fetchSearch() {
        let type = this.$el.find('select.search-type').val();
        let value = this.$el.find('[data-name="' + this.idName + '"]').val();

        if (type === 'isOneOf') {
            return  {
                type: 'linkedWith',
                field: this.name,
                value: this.searchData.oneOfIdList,
                data: {
                    type: type,
                    oneOfIdList: this.searchData.oneOfIdList,
                    oneOfNameHash: this.searchData.oneOfNameHash,
                },
            };
        }
        else if (type === 'is' || !type) {
            if (!value) {
                return false;
            }

            return  {
                type: 'linkedWith',
                field: this.name,
                value: value,
                data: {
                    type: type,
                    nameValue: this.$el.find('[data-name="' + this.nameName + '"]').val(),
                },
            };
        }
        else if (type === 'isEmpty') {
            return  {
                type: 'isNotLinked',
                data: {
                    type: type,
                },
            };
        }
        else if (type === 'isNotEmpty') {
            return  {
                type: 'isLinked',
                data: {
                    type: type,
                },
            };
        }
    }
}

export default LinkOneFieldView;
