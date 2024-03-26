

import DefaultRowActionsView from 'views/record/row-actions/default';

class ViewAndEditRowActionsView extends DefaultRowActionsView {

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
                        id: this.model.id,
                    },
                    link: '#' + this.model.entityType + '/edit/' + this.model.id,
                }
            ]);
        }

        return list;
    }
}

export default ViewAndEditRowActionsView;
