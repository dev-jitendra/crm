

import DefaultRowActionsView from 'views/record/row-actions/default';

class RelationshipActionsView extends DefaultRowActionsView {

    getActionList() {
        const list = [{
            action: 'quickView',
            label: 'View',
            data: {
                id: this.model.id
            },
            link: '#' + this.model.entityType + '/view/' + this.model.id,
        }];

        if (this.options.acl.edit && !this.options.editDisabled) {
            list.push({
                action: 'quickEdit',
                label: 'Edit',
                data: {
                    id: this.model.id,
                },
                link: '#' + this.model.entityType + '/edit/' + this.model.id,
            });
        }

        if (!this.options.unlinkDisabled) {
            list.push({
                action: 'unlinkRelated',
                label: 'Unlink',
                data: {
                    id: this.model.id,
                },
            });
        }

        this.getAdditionalActionList().forEach(item => list.push(item));

        if (this.options.acl.delete && !this.options.removeDisabled) {
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

export default RelationshipActionsView;
