

import DefaultRowActionsView from 'views/record/row-actions/default';

class EditAndRemoveRowActionsView extends DefaultRowActionsView {

    getActionList() {
        let list = [];

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id
                },
                link: '#' + this.model.entityType + '/edit/' + this.model.id
            });
        }

        if (this.options.acl.delete) {
            list.push({
                action: 'quickRemove',
                label: 'Remove',
                data: {
                    id: this.model.id,
                },
            });
        }

        return list;
    }
}

export default EditAndRemoveRowActionsView;
