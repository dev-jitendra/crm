

import ModalView from 'views/modal';

class DuplicateModalView extends ModalView {

    template = 'modals/duplicate'

    cssName = 'duplicate-modal'

    data() {
        return {
            scope: this.scope,
            duplicates: this.duplicates,
        };
    }

    setup() {
        let saveLabel = 'Save';

        if (this.model && this.model.isNew()) {
            saveLabel = 'Create';
        }

        this.buttonList = [
            {
                name: 'save',
                label: saveLabel,
                style: 'danger',
                onClick: dialog => {
                    this.trigger('save');

                    dialog.close();
                },
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        this.scope = this.options.scope;
        this.duplicates = this.options.duplicates;

        if (this.scope) {
            this.setupRecord();
        }
    }

    setupRecord() {
        let promise = new Promise(resolve => {
            this.getHelper().layoutManager.get(this.scope, 'listSmall', layout => {
                layout = Espo.Utils.cloneDeep(layout);
                layout.forEach(item => item.notSortable = true);

                this.getCollectionFactory().create(this.scope)
                    .then(collection => {
                        collection.add(this.duplicates);

                        this.createView('record', 'views/record/list', {
                            selector: '.list-container',
                            collection: collection,
                            listLayout: layout,
                            buttonsDisabled: true,
                            massActionsDisabled: true,
                            rowActionsDisabled: true,
                        });

                        resolve();
                    });
            });
        })

        this.wait(promise);
    }
}

export default DuplicateModalView;
