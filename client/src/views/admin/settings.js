

define('views/admin/settings', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        layoutName: 'settings',

        saveAndContinueEditingAction: false,

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getHelper().getAppParam('isRestrictedMode') && !this.getUser().isSuperAdmin()) {
                this.hideField('cronDisabled');
                this.hideField('maintenanceMode');
                this.setFieldReadOnly('useWebSocket');
                this.setFieldReadOnly('siteUrl');
            }
        },
    });
});
