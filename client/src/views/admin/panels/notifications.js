

define('views/admin/panels/notifications', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/panels/notifications',

        data: function () {
            return {
                notificationList: this.notificationList,
            };
        },

        setup: function () {
            this.notificationList = [];

            Espo.Ajax.getRequest('Admin/action/adminNotificationList').then(notificationList => {
                this.notificationList = notificationList;

                if (this.isRendered() || this.isBeingRendered()) {
                    this.reRender();
                }
            });
        },
    });
});
