

define('views/group-email-folder/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        rowActionsView: 'views/email-folder/record/row-actions/default',

        actionMoveUp: function (data) {
            let model = this.collection.get(data.id);

            if (!model) {
                return;
            }

            let index = this.collection.indexOf(model);

            if (index === 0) {
                return;
            }

            Espo.Ajax.postRequest('GroupEmailFolder/action/moveUp', {id: model.id})
                .then(() => {
                    this.collection.fetch();
                });
        },

        actionMoveDown: function (data) {
            let model = this.collection.get(data.id);

            if (!model) {
                return;
            }

            let index = this.collection.indexOf(model);

            if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) {
                return;
            }

            Espo.Ajax.postRequest('GroupEmailFolder/action/moveDown', {id: model.id})
                .then(() => {
                    this.collection.fetch();
                });
        },
    });
});
