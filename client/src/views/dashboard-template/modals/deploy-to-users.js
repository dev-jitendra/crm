

define('views/dashboard-template/modals/deploy-to-users', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        className: 'dialog dialog-record',

        templateContent: '<div class="record">{{{record}}}</div>',

        setup: function () {
            this.buttonList = [
                {
                    name: 'deploy',
                    text: this.translate('Deploy for Users', 'labels', 'DashboardTemplate'),
                    style: 'danger',
                },
                {
                    name: 'cancel',
                    label: 'Cancel',
                },
            ];

            this.headerText = this.model.get('name');

            this.formModel = new Model();
            this.formModel.name = 'None';

            this.formModel.setDefs({
                fields: {
                    'users': {
                        type: 'linkMultiple',
                        view: 'views/fields/users',
                        entity: 'User',
                        required: true
                    },
                    'append': {
                        type: 'bool'
                    },
                }
            });

            this.createView('record', 'views/record/edit-for-modal', {
                scope: 'None',
                model: this.formModel,
                selector: '.record',
                detailLayout: [
                    {
                        rows: [
                            [
                                {
                                    name: 'users',
                                    labelText: this.translate('users', 'links'),
                                },
                                {
                                    name: 'append',
                                    labelText: this.translate('append', 'fields', 'DashboardTemplate'),
                                }
                            ]
                        ]
                    }
                ],
            });
        },

        actionDeploy: function () {
            if (this.getView('record').processFetch()) {
                Espo.Ajax
                    .postRequest('DashboardTemplate/action/deployToUsers', {
                        id: this.model.id,
                        userIdList: this.formModel.get('usersIds'),
                        append: this.formModel.get('append'),
                    })
                    .then(() => {
                        Espo.Ui.success(this.translate('Done'));
                        this.close();
                    });
            }
        },
    });
});
