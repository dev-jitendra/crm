

define('views/inbound-email/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.setupFieldsBehaviour();
            this.initSslFieldListening();
        },

        modifyDetailLayout: function (layout) {
            layout.filter(panel => panel.tabLabel === '$label:SMTP').forEach(panel => {
                panel.rows.forEach(row => {
                    row.forEach(item => {
                        let labelText = this.translate(item.name, 'fields', 'InboundEmail');

                        if (labelText && labelText.indexOf('SMTP ') === 0) {
                            item.labelText = Espo.Utils.upperCaseFirst(labelText.substring(5));
                        }
                    });
                })
            });
        },

        wasFetched: function () {
            if (!this.model.isNew()) {
                return !!((this.model.get('fetchData') || {}).lastUID);
            }

            return false;
        },

        initSmtpFieldsControl: function () {
            this.controlSmtpFields();
            this.controlSentFolderField();
            this.listenTo(this.model, 'change:useSmtp', this.controlSmtpFields, this);
            this.listenTo(this.model, 'change:smtpAuth', this.controlSmtpFields, this);
            this.listenTo(this.model, 'change:storeSentEmails', this.controlSentFolderField, this);
        },

        controlSmtpFields: function () {
            if (this.model.get('useSmtp')) {
                this.showField('smtpHost');
                this.showField('smtpPort');
                this.showField('smtpAuth');
                this.showField('smtpSecurity');
                this.showField('smtpTestSend');
                this.showField('fromName');
                this.showField('smtpIsShared');
                this.showField('smtpIsForMassEmail');
                this.showField('storeSentEmails');

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
            this.hideField('fromName');
            this.hideField('smtpIsShared');
            this.hideField('smtpIsForMassEmail');
            this.hideField('storeSentEmails');
            this.hideField('sentFolder');

            this.setFieldNotRequired('smtpHost');
            this.setFieldNotRequired('smtpPort');
            this.setFieldNotRequired('smtpUsername');
        },

        controlSentFolderField: function () {
            if (this.model.get('useSmtp') && this.model.get('storeSentEmails')) {
                this.showField('sentFolder');
                this.setFieldRequired('sentFolder');

                return;
            }

            this.hideField('sentFolder');
            this.setFieldNotRequired('sentFolder');
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

            this.initSmtpFieldsControl();

            let handleRequirement = (model) => {
                if (model.get('createCase')) {
                    this.showField('caseDistribution');
                } else {
                    this.hideField('caseDistribution');
                }

                if (
                    model.get('createCase') &&
                    ['Round-Robin', 'Least-Busy'].indexOf(model.get('caseDistribution')) !== -1
                ) {
                    this.setFieldRequired('team');
                    this.showField('targetUserPosition');
                } else {
                    this.setFieldNotRequired('team');
                    this.hideField('targetUserPosition');
                }

                if (model.get('createCase') && 'Direct-Assignment' === model.get('caseDistribution')) {
                    this.setFieldRequired('assignToUser');
                    this.showField('assignToUser');
                } else {
                    this.setFieldNotRequired('assignToUser');
                    this.hideField('assignToUser');
                }

                if (model.get('createCase') && model.get('createCase') !== '') {
                    this.showField('team');
                } else {
                    this.hideField('team');
                }
            };

            this.listenTo(this.model, 'change:createCase', (model, value, o) => {
                handleRequirement(model);

                if (!o.ui) {
                    return;
                }

                if (!model.get('createCase')) {
                    this.model.set({
                        caseDistribution: '',
                        teamId: null,
                        teamName: null,
                        assignToUserId: null,
                        assignToUserName: null,
                        targetUserPosition: '',
                    });
                }
            });

            handleRequirement(this.model);

            this.listenTo(this.model, 'change:caseDistribution', (model, value, o) => {
                handleRequirement(model);

                if (!o.ui) {
                    return;
                }

                setTimeout(() => {
                    if (!this.model.get('caseDistribution')) {
                        this.model.set({
                            assignToUserId: null,
                            assignToUserName: null,
                            targetUserPosition: ''
                        });

                        return;
                    }

                    if (this.model.get('caseDistribution') === 'Direct-Assignment') {
                        this.model.set({
                            targetUserPosition: '',
                        });
                    }

                    this.model.set({
                        assignToUserId: null,
                        assignToUserName: null,
                    });
                }, 10);
            });
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
                if (!o.ui) {
                    return;
                }

                if (value === 'SSL') {
                    this.model.set('smtpPort', 465);
                } else if (value === 'TLS') {
                    this.model.set('smtpPort', 587);
                } else {
                    this.model.set('smtpPort', 25);
                }
            });
        },
    });
});
