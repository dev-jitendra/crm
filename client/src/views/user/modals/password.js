

define('views/user/modals/password', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        templateContent: '<div class="record no-side-margin">{{{record}}}</div>',

        className: 'dialog dialog-record',

        shortcutKeys: {
            'Control+Enter': 'apply',
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'apply',
                    label: 'Apply',
                    style: 'danger',
                },
                {
                    name: 'cancel',
                    label: 'Cancel',
                },
            ];

            this.headerHtml = '&nbsp';

            this.userModel = this.options.userModel;

            var model = this.model = new Model();
            model.name = 'UserSecurity';

            model.setDefs({
                fields: {
                    'password': {
                        type: 'password',
                        required: true,
                    },
                }
            });

            this.createView('record', 'views/record/edit-for-modal', {
                scope: 'None',
                selector: '.record',
                model: this.model,
                detailLayout: [
                    {
                        rows: [
                            [
                                {
                                    name: 'password',
                                    labelText: this.translate('yourPassword', 'fields', 'User'),
                                    params: {
                                        readyToChange: true,
                                    }
                                },
                                false
                            ]
                        ]
                    }
                ],
            });
        },

        actionApply: function () {
            var data = this.getView('record').processFetch();
            if (!data) return;

            this.trigger('proceed', data);
        },

    });
});
