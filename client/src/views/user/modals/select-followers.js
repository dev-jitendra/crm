

define('views/user/modals/select-followers', ['views/modals/select-records'], function (Dep) {

    return Dep.extend({

        setup: function () {
            this.filterList = ['active'];

            if (this.getAcl().getPermissionLevel('portalPermission')) {
                this.filterList.push('activePortal');
            }

            Dep.prototype.setup.call(this);
        },
    });
});
