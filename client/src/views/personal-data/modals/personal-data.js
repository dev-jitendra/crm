

define('views/personal-data/modals/personal-data', ['views/modal'], function (Dep) {

    return Dep.extend({

        className: 'dialog dialog-record',

        template: 'personal-data/modals/personal-data',

        backdrop: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.buttonList = [
                {
                    name: 'cancel',
                    label: 'Close'
                },
            ];

            this.headerText = this.getLanguage().translate('Personal Data');
            this.headerText += ': ' + this.model.get('name');

            if (this.getAcl().check(this.model, 'edit')) {
                this.buttonList.unshift({
                    name: 'erase',
                    label: 'Erase',
                    style: 'danger',
                    disabled: true,
                });
            }

            this.fieldList = [];

            this.scope = this.model.entityType;

            this.createView('record', 'views/personal-data/record/record', {
                selector: '.record',
                model: this.model
            }, (view) => {
                this.listenTo(view, 'check', (fieldList) => {
                    this.fieldList = fieldList;

                    if (fieldList.length) {
                        this.enableButton('erase');
                    } else {
                        this.disableButton('erase');
                    }
                });

                if (!view.fieldList.length) {
                    this.disableButton('export');
                }
            });
        },

        actionErase: function () {
            this.confirm({
                message: this.translate('erasePersonalDataConfirmation', 'messages'),
                confirmText: this.translate('Erase')
            }, () => {
                this.disableButton('erase');

                Espo.Ajax.postRequest('DataPrivacy/action/erase', {
                    fieldList: this.fieldList,
                    entityType: this.scope,
                    id: this.model.id,
                }).then(() => {
                    Espo.Ui.success(this.translate('Done'));

                    this.trigger('erase');
                })
                .catch(() => {
                    this.enableButton('erase');
                });
            });
        },
    });
});
