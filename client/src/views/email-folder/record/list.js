

define('views/email-folder/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        massUpdateDisabled: true,

        massRemoveDisabled: true,

        mergeDisabled: true,

        exportDisabled: true,

        removeDisabled: true,

        rowActionsView: 'views/email-folder/record/row-actions/default',

        actionMoveUp: function (data) {
            var model = this.collection.get(data.id);

            if (!model) {
                return;
            }

            var index = this.collection.indexOf(model);

            if (index === 0) {
                return;
            }

            Espo.Ajax.postRequest('EmailFolder/action/moveUp', {id: model.id}).then(() => {
                this.collection.fetch();
            });
        },

        actionMoveDown: function (data) {
            var model = this.collection.get(data.id);

            if (!model) {
                return;
            }

            var index = this.collection.indexOf(model);

            if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) {
                return;
            }

            Espo.Ajax.postRequest('EmailFolder/action/moveDown', {id: model.id}).then(() => {
                this.collection.fetch();
            });
        },
    });
});
