

define('views/admin/notifications', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        layoutName: 'notifications',

        saveAndContinueEditingAction: false,

        dynamicLogicDefs: {
            fields: {
                assignmentEmailNotificationsEntityList: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isTrue',
                                attribute: 'assignmentEmailNotifications',
                            }
                        ],
                    },
                },
                adminNotificationsNewVersion: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isTrue',
                                attribute: 'adminNotifications',
                            }
                        ],
                    },
                },
                adminNotificationsNewExtensionVersion: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isTrue',
                                attribute: 'adminNotifications',
                            }
                        ],
                    },
                },
            },
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.controlStreamEmailNotificationsEntityList();
            this.listenTo(this.model, 'change', function (model) {
                if (model.hasChanged('streamEmailNotifications') || model.hasChanged('portalStreamEmailNotifications')) {
                    this.controlStreamEmailNotificationsEntityList();
                }
            }, this);
        },

        controlStreamEmailNotificationsEntityList: function () {
            if (this.model.get('streamEmailNotifications') || this.model.get('portalStreamEmailNotifications')) {
                this.showField('streamEmailNotificationsEntityList');
                this.showField('streamEmailNotificationsTypeList');
            } else {
                this.hideField('streamEmailNotificationsEntityList');
                this.hideField('streamEmailNotificationsTypeList');
            }
        }

    });
});
