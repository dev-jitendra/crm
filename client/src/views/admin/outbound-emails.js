

define('views/admin/outbound-emails', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        layoutName: 'outboundEmails',

        saveAndContinueEditingAction: false,

        dynamicLogicDefs: {
            fields: {
                smtpUsername: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                            {
                                type: 'isTrue',
                                attribute: 'smtpAuth',
                            }
                        ]
                    },
                    required: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                            {
                                type: 'isTrue',
                                attribute: 'smtpAuth',
                            }
                        ]
                    }
                },
                smtpPassword: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                            {
                                type: 'isTrue',
                                attribute: 'smtpAuth',
                            }
                        ]
                    }
                },
                smtpPort: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                        ]
                    },
                    required: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                        ]
                    }
                },
                smtpSecurity: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                        ]
                    }
                },
                smtpAuth: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isNotEmpty',
                                attribute: 'smtpServer',
                            },
                        ]
                    }
                },
            },
        },

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            var smtpSecurityField = this.getFieldView('smtpSecurity');
            this.listenTo(smtpSecurityField, 'change', function () {
                var smtpSecurity = smtpSecurityField.fetch()['smtpSecurity'];
                if (smtpSecurity == 'SSL') {
                    this.model.set('smtpPort', '465');
                } else if (smtpSecurity == 'TLS') {
                    this.model.set('smtpPort', '587');
                } else {
                    this.model.set('smtpPort', '25');
                }
            }.bind(this));
        },

    });

});

