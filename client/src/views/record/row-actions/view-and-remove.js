

import DefaultRowActionsView from 'views/record/row-actions/default';

class ViewAndRemoveRowActionsView extends DefaultRowActionsView {

    getActionList() {
        
        const actionList = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.delete) {
            actionList.push({
                action: 'quickRemove',
                label: 'Remove',
                data: {
                    id: this.model.id,
                },
            });
        }

        return actionList;
    }
}

export default ViewAndRemoveRowActionsView;
