

import ModalView from 'views/modal';
import Model from 'model';

class SaveFiltersModalView extends ModalView {

    template = 'modals/save-filters'

    cssName = 'save-filters'

    data() {
        return {
            dashletList: this.dashletList,
        };
    }

    setup() {
        this.buttonList = [
            {
                name: 'save',
                label: 'Save',
                style: 'primary',
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        this.headerText = this.translate('Save Filter');

        const model = new Model();

        this.createView('name', 'views/fields/varchar', {
            selector: '.field[data-name="name"]',
            defs: {
                name: 'name',
                params: {
                    required: true
                }
            },
            mode: 'edit',
            model: model,
        });
    }

    
    getFieldView(field) {
        return this.getView(field);
    }

    actionSave() {
        const nameView = this.getFieldView('name');

        nameView.fetchToModel();

        if (nameView.validate()) {
            return;
        }

        this.trigger('save', nameView.model.get('name'));

        return true;
    }
}

export default SaveFiltersModalView;
