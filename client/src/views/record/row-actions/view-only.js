

import DefaultRowActionsView from 'views/record/row-actions/default';

class ViewOnlyRowActionsView extends DefaultRowActionsView {

    getActionList() {
        return [
            {
                action: 'quickView',
                label: 'View',
                data: {
                    id: this.model.id,
                },
                link: '#' + this.model.entityType + '/view/' + this.model.id,
            },
        ];
    }
}

export default ViewOnlyRowActionsView;
