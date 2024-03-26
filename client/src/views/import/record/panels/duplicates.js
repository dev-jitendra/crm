

import ImportImportedPanelView from 'views/import/record/panels/imported';

class ImportDuplicatesPanelView extends ImportImportedPanelView {

    link = 'duplicates'

    setup() {
        this.title = this.title || this.translate('Duplicates', 'labels', 'Import');

        super.setup();
    }

    
    actionUnmarkAsDuplicate(data) {
        const id = data.id;
        const type = data.type;

        this.confirm(this.translate('confirmation', 'messages'), () => {
            Espo.Ajax.postRequest(`Import/${this.model.id}/unmarkDuplicates`, {
                entityId: id,
                entityType: type,
            }).then(() => {
                this.collection.fetch();
            });
        });
    }
}

export default ImportDuplicatesPanelView;
