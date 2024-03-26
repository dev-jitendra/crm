

import DefaultRowActionsView from 'views/record/row-actions/default';

class ImportDuplicatesRowActionsView extends DefaultRowActionsView {

    getActionList() {
        const list = super.getActionList();

        list.push({
            action: 'unmarkAsDuplicate',
            label: 'Set as Not Duplicate',
            data: {
                id: this.model.id,
                type: this.model.entityType,
            },
        });

        return list;
    }
}

export default ImportDuplicatesRowActionsView;
