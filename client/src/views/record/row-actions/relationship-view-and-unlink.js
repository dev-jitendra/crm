

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipViewAndUnlinkActionsView extends RelationshipActionsView {

    getActionList() {
        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.edit && !this.options.unlinkDisabled) {
            list.push({
                action: 'unlinkRelated',
                label: 'Unlink',
                data: {
                    id: this.model.id,
                },
            });
        }

        return list;
    }
}

export default RelationshipViewAndUnlinkActionsView;
