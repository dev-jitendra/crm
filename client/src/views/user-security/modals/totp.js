

define('views/user-security/modals/totp', ['views/modal', 'model'], function (Dep, Model) {

    let QRCode;

    return Dep.extend({

        template: 'user-security/modals/totp',

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

            var model = new Model();

            model.name = 'UserSecurity';

            this.wait(
                Espo.Ajax
                    .postRequest('UserSecurity/action/getTwoFactorUserSetupData', {
                        id: this.model.id,
                        password: this.model.get('password'),
                        auth2FAMethod: this.model.get('auth2FAMethod'),
                        reset: this.options.reset,
                    })
                    .then(data => {
                        this.label = data.label;
                        this.secret = data.auth2FATotpSecret;

                        model.set('secret', data.auth2FATotpSecret);
                    })
            );

            model.setDefs({
                fields: {
                    'code': {
                        type: 'varchar',
                        required: true,
                        maxLength: 7,
                    },
                    'secret': {
                        type: 'varchar',
                        readOnly: true,
                    },
                }
            });

            this.createView('record', 'views/record/edit-for-modal', {
                scope: 'None',
                selector: '.record',
                model: model,
                detailLayout: [
                    {
                        rows: [
                            [
                                {
                                    name: 'secret',
                                    labelText: this.translate('Secret', 'labels', 'User'),
                                },
                                false
                            ],
                            [
                                {
                                    name: 'code',
                                    labelText: this.translate('Code', 'labels', 'User'),
                                },
                                false
                            ]
                        ]
                    }
                ],
            });

            Espo.loader.requirePromise('lib!qrcodejs').then(lib => {
                QRCode = lib;
            })
        },

        afterRender: function () {
            new QRCode(this.$el.find('.qrcode').get(0), {
                text: 'otpauth:
                width: 256,
                height: 256,
                colorDark : '#000000',
                colorLight : '#ffffff',
                correctLevel : QRCode.CorrectLevel.H,
            });
        },

        actionApply: function () {
            var data = this.getView('record').processFetch();

            if (!data) {
                return;
            }

            this.model.set('code', data.code);

            this.hideButton('apply');
            this.hideButton('cancel');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            this.model
                .save()
                .then(() => {
                    Espo.Ui.notify(false);

                    this.trigger('done');
                })
                .catch(() => {
                    this.showButton('apply');
                    this.showButton('cancel');
                });
        },

    });
});
