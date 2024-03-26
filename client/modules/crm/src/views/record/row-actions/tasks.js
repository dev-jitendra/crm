

define('crm:views/record/row-actions/tasks', ['views/record/row-actions/relationship-no-unlink'], function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var list = [{
                action: 'quickView',
                label: 'View',
                data: {
                    id: this.model.id
                },
                link: '#' + this.model.entityType + '/view/' + this.model.id
            }];

            if (this.options.acl.edit) {
                list.push({
                    action: 'quickEdit',
                    label: 'Edit',
                    data: {
                        id: this.model.id
                    },
                    link: '#' + this.model.entityType + '/edit/' + this.model.id
                });

                if (!~['Completed', 'Canceled'].indexOf(this.model.get('status'))) {
                    list.push({
                        action: 'Complete',
                        text: this.translate('Complete', 'labels', 'Task'),
                        data: {
                            id: this.model.id
                        }
                    });
                }
            }

            if (this.options.acl.delete) {
                list.push({
                    action: 'removeRelated',
                    label: 'Remove',
                    data: {
                        id: this.model.id
                    }
                });
            }

            return list;
        },
    });
});
