

define('views/user-security/modals/two-factor-sms',
    ['views/modal', 'model'],
    function (Dep, Model) {

    return Dep.extend({

        template: 'user-security/modals/two-factor-sms',

        className: 'dialog dialog-record',

        shortcutKeys: {
            'Control+Enter': 'apply',
        },

        events: {
            'click [data-action="sendCode"]': function () {
                this.actionSendCode();
            },
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'apply',
                    label: 'Apply',
                    style: 'danger',
                    hidden: true,
                },
                {
                    name: 'cancel',
                    label: 'Cancel',
                },
            ];

            this.headerHtml = '&nbsp';

            let codeLength = this.getConfig().get('auth2FASmsCodeLength') || 7;

            let model = new Model();

            model.name = 'UserSecurity';

            model.set('phoneNumber', null);

            model.setDefs({
                fields: {
                    'code': {
                        type: 'varchar',
                        required: true,
                        maxLength: codeLength,
                    },
                    'phoneNumber': {
                        type: 'enum',
                        required: true,
                    },
                }
            });

            this.internalModel = model;

            this.wait(
                Espo.Ajax
                    .postRequest('UserSecurity/action/getTwoFactorUserSetupData', {
                        id: this.model.id,
                        password: this.model.get('password'),
                        auth2FAMethod: this.model.get('auth2FAMethod'),
                        reset: this.options.reset,
                    })
                    .then(data => {
                        this.phoneNumberList = data.phoneNumberList;

                        this.createView('record', 'views/record/edit-for-modal', {
                            scope: 'None',
                            selector: '.record',
                            model: model,
                            detailLayout: [
                                {
                                    rows: [
                                        [
                                            {
                                                name: 'phoneNumber',
                                                labelText: this.translate('phoneNumber', 'fields', 'User'),
                                            },
                                            false
                                        ],
                                        [
                                            {
                                                name: 'code',
                                                labelText: this.translate('Code', 'labels', 'User'),
                                            },
                                            false
                                        ],
                                    ]
                                }
                            ],
                        }, view => {
                            view.setFieldOptionList('phoneNumber', this.phoneNumberList);

                            if (this.phoneNumberList.length) {
                                model.set('phoneNumber', this.phoneNumberList[0]);
                            }

                            view.hideField('code');
                        });
                    })
            );
        },

        afterRender: function () {
            this.$sendCode = this.$el.find('[data-action="sendCode"]');

            this.$pInfo = this.$el.find('p.p-info');
            this.$pButton = this.$el.find('p.p-button');
            this.$pInfoAfter = this.$el.find('p.p-info-after');
        },

        actionSendCode: function () {
            this.$sendCode.attr('disabled', 'disabled').addClass('disabled');

            Espo.Ajax
                .postRequest('TwoFactorSms/action/sendCode', {
                    id: this.model.id,
                    phoneNumber: this.internalModel.get('phoneNumber'),
                })
                .then(() => {
                    this.showButton('apply');

                    this.$pInfo.addClass('hidden');
                    this.$pButton.addClass('hidden');
                    this.$pInfoAfter.removeClass('hidden');

                    this.getView('record').setFieldReadOnly('phoneNumber');
                    this.getView('record').showField('code');
                })
                .catch(() => {
                    this.$sendCode.removeAttr('disabled').removeClass('disabled');
                });
        },

        actionApply: function () {
            let data = this.getView('record').processFetch();

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
