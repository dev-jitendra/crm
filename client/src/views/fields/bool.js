



import BaseFieldView from 'views/fields/base';
import Select from 'ui/select';


class BoolFieldView extends BaseFieldView {

    type = 'bool'

    listTemplate = 'fields/bool/list'
    detailTemplate = 'fields/bool/detail'
    editTemplate = 'fields/bool/edit'
    searchTemplate = 'fields/bool/search'

    validations = []
    initialSearchIsNotIdle = true

    
    data() {
        let data = super.data();

        data.valueIsSet = this.model.has(this.name);

        return data;
    }

    afterRender() {
        super.afterRender();

        if (this.mode === this.MODE_SEARCH) {
            this.$element.on('change', () => {
                this.trigger('change');
            });

            Select.init(this.$element);
        }
    }

    fetch() {
        let value = this.$element.get(0).checked;

        let data = {};

        data[this.name] = value;

        return data;
    }

    fetchSearch() {
        let type = this.$element.val();

        if (!type) {
            return null;
        }

        if (type === 'any') {
            return {
                type: 'or',
                value: [
                    {
                        type: 'isTrue',
                        attribute: this.name,

                    },
                    {
                        type: 'isFalse',
                        attribute: this.name,
                    },
                ],
                data: {
                    type: type,
                },
            };
        }

        return {
            type: type,
            data: {
                type: type,
            },
        };
    }

    getSearchType() {
        return this.getSearchParamsData().type ||
            this.searchParams.type ||
            'isTrue';
    }
}

export default BoolFieldView;
