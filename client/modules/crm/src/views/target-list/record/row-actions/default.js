

define('crm:views/target-list/record/row-actions/default', ['views/record/row-actions/relationship'], function (Dep) {

    return Dep.extend({

        getActionList: function () {
            const list = Dep.prototype.getActionList.call(this);

            if (this.options.acl.edit) {
                if (this.model.get('targetListIsOptedOut')) {
                    list.push({
                        action: 'cancelOptOut',
                        label: 'Cancel Opt-Out',
                        data: {
                            id: this.model.id,
                            type: this.model.entityType,
                        },
                    });
                } else {
                    list.push({
                        action: 'optOut',
                        label: 'Opt-Out',
                        data: {
                            id: this.model.id,
                            type: this.model.entityType,
                        },
                    });
                }
            }

            return list;
        },
    });
});
