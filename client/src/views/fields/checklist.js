



import ArrayFieldView from 'views/fields/array';

class ChecklistFieldView extends ArrayFieldView {

    type = 'checklist'

    listTemplate = 'fields/array/list'
    detailTemplate = 'fields/checklist/detail'
    editTemplate = 'fields/checklist/edit'

    isInversed = false

    events = {}

    data() {
        return {
            optionDataList: this.getOptionDataList(),
            ...super.data(),
        };
    }

    setup() {
        super.setup();

        this.params.options = this.params.options || [];

        this.isInversed = this.params.isInversed || this.options.isInversed || this.isInversed;
    }

    afterRender() {
        if (this.isSearchMode()) {
            this.renderSearch();
        }

        if (this.isEditMode()) {
            this.$el.find('input').on('change', () => {
                this.trigger('change');
            });
        }
    }

    getOptionDataList() {
        let valueList = this.model.get(this.name) || [];
        let list = [];

        this.params.options.forEach((item) => {
            let isChecked = ~valueList.indexOf(item);
            let dataName = item;
            let id = this.cid + '-' + Espo.Utils.camelCaseToHyphen(item.replace(/\s+/g, '-'));

            if (this.isInversed) {
                isChecked = !isChecked;
            }

            list.push({
                name: item,
                isChecked: isChecked,
                dataName: dataName,
                id: id,
                label: this.translatedOptions[item] || item,
            });
        });

        return list;
    }

    fetch() {
        let list = [];

        this.params.options.forEach(item => {
            let $item = this.$el.find('input[data-name="' + item + '"]');

            let isChecked = $item.get(0) && $item.get(0).checked;

            if (this.isInversed) {
                isChecked = !isChecked;
            }

            if (isChecked) {
                list.push(item);
            }
        });

        let data = {};

        data[this.name] = list;

        return data;
    }

    validateRequired() {
        if (!this.isRequired()) {
            return;
        }

        let value = this.model.get(this.name);

        if (!value || value.length === 0) {
            let msg = this.translate('fieldIsRequired', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg, '.checklist-item-container:last-child input');

            return true;
        }
    }

    validateMaxCount() {
        if (!this.params.maxCount) {
            return;
        }

        let itemList = this.model.get(this.name) || [];

        if (itemList.length > this.params.maxCount) {
            let msg =
                this.translate('fieldExceedsMaxCount', 'messages')
                    .replace('{field}', this.getLabelText())
                    .replace('{maxCount}', this.params.maxCount.toString());

            this.showValidationMessage(msg, '.checklist-item-container:last-child input');

            return true;
        }
    }
}

export default ChecklistFieldView;
