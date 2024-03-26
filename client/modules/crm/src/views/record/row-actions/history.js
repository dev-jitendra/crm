

define('crm:views/record/row-actions/history', ['views/record/row-actions/relationship'], function (Dep) {

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

            if (this.model.entityType === 'Email') {
                list.push({
                    action: 'reply',
                    text: this.translate('Reply', 'labels', 'Email'),
                    data: {
                        id: this.model.id
                    }
                });
            }

            if (this.options.acl.edit) {
                list = list.concat([
                    {
                        action: 'quickEdit',
                        label: 'Edit',
                        data: {
                            id: this.model.id
                        },
                        link: '#' + this.model.entityType + '/edit/' + this.model.id
                    }
                ]);
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
