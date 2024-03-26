

define('views/email-account/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.setupFieldsBehaviour();
            this.initSslFieldListening();
            this.initSmtpFieldsControl();

            if (this.getUser().isAdmin()) {
                this.setFieldNotReadOnly('assignedUser');
            } else {
                this.setFieldReadOnly('assignedUser');
            }
        },

        modifyDetailLayout: function (layout) {
            layout.filter(panel => panel.tabLabel === '$label:SMTP').forEach(panel => {
                panel.rows.forEach(row => {
                    row.forEach(item => {
                        let labelText = this.translate(item.name, 'fields', 'EmailAccount');

                        if (labelText && labelText.indexOf('SMTP ') === 0) {
                            item.labelText = Espo.Utils.upperCaseFirst(labelText.substring(5));
                        }
                    });
                })
            });
        },

        setupFieldsBehaviour: function () {
            this.controlStatusField();

            this.listenTo(this.model, 'change:status', (model, value, o) => {
                if (o.ui) {
                    this.controlStatusField();
                }
            });

            this.listenTo(this.model, 'change:useImap', (model, value, o) => {
                if (o.ui) {
                    this.controlStatusField();
                }
            });

            if (this.wasFetched()) {
                this.setFieldReadOnly('fetchSince');
            } else {
                this.setFieldNotReadOnly('fetchSince');
            }
        },

        controlStatusField: function () {
            let list = ['username', 'port', 'host', 'monitoredFolders'];

            if (this.model.get('status') === 'Active' && this.model.get('useImap')) {
                list.forEach(item => {
                    this.setFieldRequired(item);
                });

                return;
            }

            list.forEach(item => {
                this.setFieldNotRequired(item);
            });
        },

        wasFetched: function () {
            if (!this.model.isNew()) {
                return !!((this.model.get('fetchData') || {}).lastUID);
            }

            return false;
        },

        initSslFieldListening: function () {
            this.listenTo(this.model, 'change:security', (model, value, o) => {
                if (!o.ui) {
                    return;
                }

                if (value) {
                    this.model.set('port', 993);
                } else {
                    this.model.set('port', 143);
                }
            });

            this.listenTo(this.model, 'change:smtpSecurity', (model, value, o) => {
                if (o.ui) {
                    if (value === 'SSL') {
                        this.model.set('smtpPort', 465);
                    } else if (value === 'TLS') {
                        this.model.set('smtpPort', 587);
                    } else {
                        this.model.set('smtpPort', 25);
                    }
                }
            });
        },

        initSmtpFieldsControl: function () {
            this.controlSmtpFields();

            this.listenTo(this.model, 'change:useSmtp', this.controlSmtpFields, this);
            this.listenTo(this.model, 'change:smtpAuth', this.controlSmtpFields, this);
        },

        controlSmtpFields: function () {
            if (this.model.get('useSmtp')) {
                this.showField('smtpHost');
                this.showField('smtpPort');
                this.showField('smtpAuth');
                this.showField('smtpSecurity');
                this.showField('smtpTestSend');

                this.setFieldRequired('smtpHost');
                this.setFieldRequired('smtpPort');

                this.controlSmtpAuthField();

                return;
            }

            this.hideField('smtpHost');
            this.hideField('smtpPort');
            this.hideField('smtpAuth');
            this.hideField('smtpUsername');
            this.hideField('smtpPassword');
            this.hideField('smtpAuthMechanism');
            this.hideField('smtpSecurity');
            this.hideField('smtpTestSend');

            this.setFieldNotRequired('smtpHost');
            this.setFieldNotRequired('smtpPort');
            this.setFieldNotRequired('smtpUsername');
        },

        controlSmtpAuthField: function () {
            if (this.model.get('smtpAuth')) {
                this.showField('smtpUsername');
                this.showField('smtpPassword');
                this.showField('smtpAuthMechanism');
                this.setFieldRequired('smtpUsername');

                return;
            }

            this.hideField('smtpUsername');
            this.hideField('smtpPassword');
            this.hideField('smtpAuthMechanism');
            this.setFieldNotRequired('smtpUsername');
        },
    });
});
