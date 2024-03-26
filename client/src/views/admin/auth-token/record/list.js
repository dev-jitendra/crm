

define('views/admin/auth-token/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        rowActionsView: 'views/admin/auth-token/record/row-actions/default',

        massActionList: ['remove', 'setInactive'],

        checkAllResultMassActionList: ['remove', 'setInactive'],

        massActionSetInactive: function () {
            let ids = null;
            let allResultIsChecked = this.allResultIsChecked;

            if (!allResultIsChecked) {
                ids = this.checkedList;
            }

            let attributes = {
                isActive: false,
            };

            Espo.Ajax
                .postRequest('MassAction', {
                    action: 'update',
                    entityType: this.entityType,
                    params: {
                        ids: ids || null,
                        where: (!ids || ids.length === 0) ? this.collection.getWhere() : null,
                        searchParams: (!ids || ids.length === 0) ? this.collection.data : null,
                    },
                    data: attributes,
                })
                .then(() => {
                    this.collection
                        .fetch()
                        .then(() => {
                            Espo.Ui.success(this.translate('Done'));

                            if (ids) {
                                ids.forEach(id => {
                                    this.checkRecord(id);
                                });
                            }
                        });
                });
        },

        actionSetInactive: function (data) {
            if (!data.id) {
                return;
            }

            var model = this.collection.get(data.id);

            if (!model) {
                return;
            }

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            model
                .save({'isActive': false}, {patch: true})
                .then(() => {
                    Espo.Ui.notify(false);
                });
        },
    });
});
