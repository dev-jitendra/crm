

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipUnlinkOnlyActionsView extends RelationshipActionsView {

    getActionList() {
        if (this.options.acl.edit && !this.options.unlinkDisabled) {
            return [
                {
                    action: 'unlinkRelated',
                    label: 'Unlink',
                    data: {
                        id: this.model.id,
                    },
                },
            ];
        }
    }
}


export default RelationshipUnlinkOnlyActionsView;
