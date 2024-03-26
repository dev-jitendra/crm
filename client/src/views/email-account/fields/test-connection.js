

define('views/email-account/fields/test-connection', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        readOnly: true,

        templateContent:
            '<button class="btn btn-default disabled" data-action="testConnection">'+
            '{{translate \'Test Connection\' scope=\'EmailAccount\'}}</button>',

        url: 'EmailAccount/action/testConnection',

        events: {
            'click [data-action="testConnection"]': function () {
                this.test();
            },
        },

        fetch: function () {
            return {};
        },

        checkAvailability: function () {
            if (this.model.get('host')) {
                this.$el.find('button').removeClass('disabled').removeAttr('disabled');
            } else {
                this.$el.find('button').addClass('disabled').attr('disabled', 'disabled');
            }
        },

        afterRender: function () {
            this.checkAvailability();

            this.stopListening(this.model, 'change:host');

            this.listenTo(this.model, 'change:host', () => {
                this.checkAvailability();
            });
        },

        getData: function () {
            return {
                'host': this.model.get('host'),
                'port': this.model.get('port'),
                'security': this.model.get('security'),
                'username': this.model.get('username'),
                'password': this.model.get('password') || null,
                'id': this.model.id,
                emailAddress: this.model.get('emailAddress'),
                userId: this.model.get('assignedUserId'),
            };
        },

        test: function () {
            let data = this.getData();

            let $btn = this.$el.find('button');

            $btn.addClass('disabled');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Espo.Ajax.postRequest(this.url, data)
                .then(() => {
                    $btn.removeClass('disabled');

                    Espo.Ui.success(this.translate('connectionIsOk', 'messages', 'EmailAccount'));
                })
                .catch(xhr => {
                    let statusReason = xhr.getResponseHeader('X-Status-Reason') || '';
                    statusReason = statusReason.replace(/ $/, '');
                    statusReason = statusReason.replace(/,$/, '');

                    let msg = this.translate('Error');

                    if (parseInt(xhr.status) !== 200) {
                        msg += ' ' + xhr.status;
                    }

                    if (statusReason) {
                        msg += ': ' + statusReason;
                    }

                    Espo.Ui.error(msg, true);

                    console.error(msg);

                    xhr.errorIsHandled = true;

                    $btn.removeClass('disabled');
                });
        },
    });
});
