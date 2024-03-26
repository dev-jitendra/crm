

define('crm:views/meeting/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        rowActionsView: 'crm:views/meeting/record/row-actions/default',

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getAcl().checkScope(this.entityType, 'edit')) {
                this.massActionList.push('setHeld');
                this.massActionList.push('setNotHeld');
            }
        },

        actionSetHeld: function (data) {
            let id = data.id;

            if (!id) {
                return;
            }

            let model = this.collection.get(id);

            if (!model) {
                return;
            }

            model.set('status', 'Held');

            this.listenToOnce(model, 'sync', () => {
                Espo.Ui.notify(false);

                this.collection.fetch();
            });

            Espo.Ui.notify(this.translate('saving', 'messages'));

            model.save();
        },

        actionSetNotHeld: function (data) {
            let id = data.id;

            if (!id) {
                return;
            }

            var model = this.collection.get(id);

            if (!model) {
                return;
            }

            model.set('status', 'Not Held');

            this.listenToOnce(model, 'sync', () => {
                Espo.Ui.notify(false);
                this.collection.fetch();
            });

            Espo.Ui.notify(this.translate('saving', 'messages'));

            model.save();
        },

        massActionSetHeld: function () {
            Espo.Ui.notify(this.translate('saving', 'messages'));

            let data = {ids: this.checkedList};

            Espo.Ajax.postRequest(this.collection.entityType + '/action/massSetHeld', data)
                .then(() => {
                    Espo.Ui.notify(false);

                    this.listenToOnce(this.collection, 'sync', () => {
                        data.ids.forEach(id => {
                            if (this.collection.get(id)) {
                                this.checkRecord(id);
                            }
                        });
                    });

                    this.collection.fetch();
                });
        },

        massActionSetNotHeld: function () {
            Espo.Ui.notify(this.translate('saving', 'messages'));

            let data = {ids: this.checkedList};

            Espo.Ajax.postRequest(this.collection.entityType + '/action/massSetNotHeld', data)
                .then(() => {
                    Espo.Ui.notify(false);

                    this.listenToOnce(this.collection, 'sync', () => {
                        data.ids.forEach(id => {
                            if (this.collection.get(id)) {
                                this.checkRecord(id);
                            }
                        });
                    });

                    this.collection.fetch();
                });
        },

    });
});
