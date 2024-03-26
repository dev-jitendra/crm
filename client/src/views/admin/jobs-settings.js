

define('views/admin/jobs-settings', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        layoutName: 'jobsSettings',

        saveAndContinueEditingAction: false,

        dynamicLogicDefs: {
            fields: {
                jobPoolConcurrencyNumber: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isTrue',
                                attribute: 'jobRunInParallel'
                            }
                        ]
                    }
                }
            }
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getHelper().getAppParam('isRestrictedMode') && !this.getUser().isSuperAdmin()) {

                this.setFieldReadOnly('jobRunInParallel');
                this.setFieldReadOnly('jobMaxPortion');
                this.setFieldReadOnly('jobPoolConcurrencyNumber');
                this.setFieldReadOnly('daemonInterval');
                this.setFieldReadOnly('daemonMaxProcessNumber');
                this.setFieldReadOnly('daemonProcessTimeout');
            }
        },

    });
});
