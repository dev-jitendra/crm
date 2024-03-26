

import DefaultRowActionsView from 'views/record/row-actions/default';

class DefaultKanbanRowActionsView extends DefaultRowActionsView {

    getActionList() {
        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id,
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.statusFieldIsEditable) {
            list.push({
                action: 'moveOver',
                label: 'Move Over',
                data: {
                    id: this.model.id,
                },
            });
        }

        if (this.options.acl.edit) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id
                },
                link: '#' + this.model.entityType + '/edit/' + this.model.id,
            });
        }

        this.getAdditionalActionList().forEach(item => list.push(item));

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

export default DefaultKanbanRowActionsView;
