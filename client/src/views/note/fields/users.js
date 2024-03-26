

define('views/note/fields/users', ['views/fields/link-multiple'], function (Dep) {

    return Dep.extend({

        init: function () {
            this.messagePermission = this.getAcl().getPermissionLevel('message');
            this.portalPermission = this.getAcl().getPermissionLevel('portal');

            if (this.messagePermission === 'no' && this.portalPermission === 'no') {
                this.readOnly = true;
            }

            Dep.prototype.init.call(this);
        },

        getSelectBoolFilterList: function () {
            if (this.messagePermission === 'team') {
                return ['onlyMyTeam'];
            }

            if (this.portalPermission === 'yes') {
                return null;
            }
        },

        getSelectPrimaryFilterName: function () {
            if (this.portalPermission === 'yes' && this.messagePermission === 'no') {
                return 'activePortal';
            }

            return 'active';
        },

        getSelectFilterList: function () {

            if (this.portalPermission === 'yes') {
                if (this.messagePermission === 'no') {
                     return ['activePortal'];
                }

                return ['active', 'activePortal'];
            }

            return null;
        },

    });
});
