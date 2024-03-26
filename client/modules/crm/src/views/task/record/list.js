

define('crm:views/task/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        rowActionsView: 'crm:views/task/record/row-actions/default',

        actionSetCompleted: function (data) {
            var id = data.id;

            if (!id) {
                return;
            }

            var model = this.collection.get(id);

            if (!model) {
                return;
            }

            model.set('status', 'Completed');

            this.listenToOnce(model, 'sync', () => {
                Espo.Ui.notify(false);
                this.collection.fetch();
            });

            Espo.Ui.notify(this.translate('saving', 'messages'));
            model.save();
        },
    });
});
