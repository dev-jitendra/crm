

define('views/dashboard-template/modals/deploy-to-team', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        className: 'dialog dialog-record',

        templateContent: '<div class="record">{{{record}}}</div>',

        setup: function () {
            this.buttonList = [
                {
                    name: 'deploy',
                    text: this.translate('Deploy for Team', 'labels', 'DashboardTemplate'),
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
                    'team': {
                        type: 'link',
                        entity: 'Team',
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
                                    name: 'team',
                                    labelText: this.translate('team', 'links'),
                                },
                                {
                                    name: 'append',
                                    labelText: this.translate('append', 'fields', 'DashboardTemplate'),
                                },
                            ]
                        ]
                    }
                ],
            });
        },

        actionDeploy: function () {
            if (this.getView('record').processFetch()) {
                Espo.Ajax
                    .postRequest('DashboardTemplate/action/deployToTeam', {
                        id: this.model.id,
                        teamId: this.formModel.get('teamId'),
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
