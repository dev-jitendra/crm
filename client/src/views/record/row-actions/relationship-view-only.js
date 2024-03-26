

import RelationshipActionsView from 'views/record/row-actions/relationship';

class RelationshipViewOnlyActionsView extends RelationshipActionsView {

    getActionList() {
        return [
            {
                action: 'viewRelated',
                label: 'View',
                data: {
                    id: this.model.id,
                },
                link: '#' + this.model.entityType + '/view/' + this.model.id,
            }
        ];
    }
}

export default RelationshipViewOnlyActionsView;
