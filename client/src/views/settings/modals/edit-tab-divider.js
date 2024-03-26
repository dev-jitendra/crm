

import ModalView from 'views/modal';
import Model from 'model';

class EditTabDividerSettingsModalView extends ModalView {

    className = 'dialog dialog-record'

    templateContent = '<div class="record no-side-margin">{{{record}}}</div>'

    setup() {
        super.setup();

        this.headerText = this.translate('Divider', 'labels', 'Settings');

        this.buttonList.push({
            name: 'apply',
            label: 'Apply',
            style: 'danger',
        });

        this.buttonList.push({
            name: 'cancel',
            label: 'Cancel',
        });

        this.shortcutKeys = {
            'Control+Enter': () => this.actionApply(),
        };

        let detailLayout = [
            {
                rows: [
                    [
                        {
                            name: 'text',
                            labelText: this.translate('label', 'fields', 'Admin'),
                        },
                        false,
                    ],
                ]
            }
        ];

        let model = this.model = new Model({}, {entityType: 'Dummy'});

        model.set(this.options.itemData);
        model.setDefs({
            fields: {
                text: {
                    type: 'varchar',
                },
            },
        });

        this.createView('record', 'views/record/edit-for-modal', {
            detailLayout: detailLayout,
            model: model,
            selector: '.record',
        });
    }

    
    actionApply() {
        let recordView =  this.getView('record');

        if (recordView.validate()) {
            return;
        }

        let data = recordView.fetch();

        this.trigger('apply', data);
    }
}


export default EditTabDividerSettingsModalView;
