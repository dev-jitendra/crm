

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipNoUnlinkActionsView extends RelationshipActionsView {

    getActionList() {
        let list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.edit) {
            list = list.concat([
                {
                    action: 'quickEdit',
                    label: 'Edit',
                    data: {
                        id: this.model.id
                    },
                    link: '#' + this.model.entityType + '/edit/' + this.model.id,
                }
            ]);
        }

        if (this.options.acl.delete) {
            list.push({
                action: 'removeRelated',
                label: 'Remove',
                data: {
                    id: this.model.id,
                },
            });
        }

        return list;
    }
}


export default RelationshipNoUnlinkActionsView;
