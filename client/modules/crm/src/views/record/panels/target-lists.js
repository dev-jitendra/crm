

define('crm:views/record/panels/target-lists', ['views/record/panels/relationship'], function (Dep) {

    return Dep.extend({

        actionOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                Espo.Ajax
                    .postRequest('TargetList/action/optOut', {
                        id: data.id,
                        targetId: this.model.id,
                        targetType: this.model.entityType,
                    })
                    .then(() => {
                        this.collection.fetch();
                        Espo.Ui.success(this.translate('Done'));
                        this.model.trigger('opt-out');
                    });
            });
        },

        actionCancelOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                Espo.Ajax
                    .postRequest('TargetList/action/cancelOptOut', {
                        id: data.id,
                        targetId: this.model.id,
                        targetType: this.model.entityType,
                    })
                    .then(() => {
                        this.collection.fetch();
                        Espo.Ui.success(this.translate('Done'));
                        this.model.trigger('cancel-opt-out');
                    });
            });
        },
    });
});
