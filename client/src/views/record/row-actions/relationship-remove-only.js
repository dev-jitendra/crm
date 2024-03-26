

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipRemoveOnlyActionsView extends RelationshipActionsView {

    getActionList() {
        if (this.options.acl.delete) {
            return [
                {
                    action: 'removeRelated',
                    label: 'Remove',
                    data: {
                        id: this.model.id,
                    },
                },
            ];
        }
    }
}


export default RelationshipRemoveOnlyActionsView;
