

define('crm:views/target-list/record/panels/relationship', ['views/record/panels/relationship'], function (Dep) {

    return Dep.extend({

        fetchOnModelAfterRelate: true,

        actionOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                Espo.Ajax
                    .postRequest('TargetList/action/optOut', {
                        id: this.model.id,
                        targetId: data.id,
                        targetType: data.type,
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
                        id: this.model.id,
                        targetId: data.id,
                        targetType: data.type,
                    })
                    .then(() => {
                        this.collection.fetch();
                        Espo.Ui.success(this.translate('Done'));

                        this.collection.fetch();
                        this.model.trigger('cancel-opt-out');
                    });
            });
        },
    });
});
