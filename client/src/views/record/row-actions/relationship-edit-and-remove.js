

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipEditAndRemoveActionsView extends RelationshipActionsView {

    getActionList() {
        const list = [];

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id,
                },
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

export default RelationshipEditAndRemoveActionsView;
