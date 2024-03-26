

define('views/admin/authentication/fields/test-connection', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        templateContent: `
            <button
                class="btn btn-default"
                data-action="testConnection"
            >{{translate \'Test Connection\' scope=\'Settings\'}}</button>
        `,

        events: {
            'click [data-action="testConnection"]': function () {
                this.testConnection();
            },
        },

        fetch: function () {
            return {};
        },

        getConnectionData: function () {
            return {
                'host': this.model.get('ldapHost'),
                'port': this.model.get('ldapPort'),
                'useSsl': this.model.get('ldapSecurity'),
                'useStartTls': this.model.get('ldapSecurity'),
                'username': this.model.get('ldapUsername'),
                'password': this.model.get('ldapPassword'),
                'bindRequiresDn': this.model.get('ldapBindRequiresDn'),
                'accountDomainName': this.model.get('ldapAccountDomainName'),
                'accountDomainNameShort': this.model.get('ldapAccountDomainNameShort'),
                'accountCanonicalForm': this.model.get('ldapAccountCanonicalForm'),
            };
        },

        testConnection: function () {
            let data = this.getConnectionData();

            this.$el.find('button').prop('disabled', true);

            this.notify('Connecting', null, null, 'Settings');

            Espo.Ajax
                .postRequest('Ldap/action/testConnection', data)
                .then(() => {
                    this.$el.find('button').prop('disabled', false);

                    Espo.Ui.success(this.translate('ldapTestConnection', 'messages', 'Settings'));
                })
                .catch(xhr => {
                    let statusReason = xhr.getResponseHeader('X-Status-Reason') || '';
                    statusReason = statusReason.replace(/ $/, '');
                    statusReason = statusReason.replace(/,$/, '');

                    let msg = this.translate('Error') + ' ' + xhr.status;

                    if (statusReason) {
                        msg += ': ' + statusReason;
                    }

                    Espo.Ui.error(msg, true);

                    console.error(msg);

                    xhr.errorIsHandled = true;

                    this.$el.find('button').prop('disabled', false);
                });
        },
    });
});
