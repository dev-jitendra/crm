

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipViewAndEditActionsView extends RelationshipActionsView {

    getActionList() {
        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id,
                },
                link: '#' + this.model.entityType + '/edit/' + this.model.id,
            });
        }

        return list;
    }
}

export default RelationshipViewAndEditActionsView;

