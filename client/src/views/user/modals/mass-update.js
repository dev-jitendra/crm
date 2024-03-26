

define('views/user/modals/mass-update', ['views/modals/mass-update'], function (Dep) {

    return Dep.extend({

        setup: function () {

            if (this.options.scope === 'ApiUser') {
                this.layoutName = 'massUpdateApi';
            } else if (this.options.scope === 'PortalUser') {
                this.layoutName = 'massUpdatePortal';
            }

            Dep.prototype.setup.call(this);
        },
    });
});
