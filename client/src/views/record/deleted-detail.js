

import DetailRecordView from 'views/record/detail';

class DeletedDetailRecordView extends DetailRecordView {

    bottomView = null

    sideView = 'views/record/deleted-detail-side'

    setupBeforeFinal() {
        super.setupBeforeFinal();

        this.buttonList = [];
        this.dropdownItemList = [];

        this.addDropdownItem({
            name: 'restoreDeleted',
            label: 'Restore'
        });
    }

    
    actionRestoreDeleted() {
        Espo.Ui.notify(' ... ');

        Espo.Ajax
            .postRequest(this.model.entityType + '/action/restoreDeleted', {id: this.model.id})
            .then(() => {
                Espo.Ui.notify(false);

                this.model.set('deleted', false);
                this.model.trigger('after:restore-deleted');
            });
    }
}

export default DeletedDetailRecordView;
