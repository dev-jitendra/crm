

import ImportImportedPanelView from 'views/import/record/panels/imported';

class ImportUpdatedPanelView extends ImportImportedPanelView {

    link = 'updated'
    rowActionsView = 'views/record/row-actions/relationship-view-and-edit'

    setup() {
        this.title = this.title || this.translate('Updated', 'labels', 'Import');

        super.setup();
    }
}

export default ImportUpdatedPanelView;
