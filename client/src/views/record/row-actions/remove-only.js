

import DefaultRowActionsView from 'views/record/row-actions/default';

class RemoveOnlyRowActionsView extends DefaultRowActionsView {

    getActionList() {
        if (this.options.acl.delete) {
            return [
                {
                    action: 'quickRemove',
                    label: 'Remove',
                    data: {
                        id: this.model.id,
                    },
                }
            ];
        }
    }
}

export default RemoveOnlyRowActionsView;
