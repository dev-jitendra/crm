

import RelationshipPanelView from 'views/record/panels/relationship';

class ImportImportedPanelView extends RelationshipPanelView {

    link = 'imported'
    readOnly = true
    rowActionsView = 'views/record/row-actions/relationship-no-unlink'

    setup() {
        this.entityType = this.model.get('entityType');
        this.title = this.title || this.translate('Imported', 'labels', 'Import');

        super.setup();
    }
}

export default ImportImportedPanelView;

