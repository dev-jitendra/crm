

define('crm:views/meeting/fields/users', ['crm:views/meeting/fields/attendees'], function (Dep) {

    return Dep.extend({

        selectPrimaryFilterName: 'active',

        init: function () {
            this.assignmentPermission = this.getAcl().getPermissionLevel('assignmentPermission');

            if (this.assignmentPermission === 'no') {
                this.readOnly = true;
            }

            Dep.prototype.init.call(this);
        },

        getSelectBoolFilterList: function () {
            if (this.assignmentPermission === 'team') {
                return ['onlyMyTeam'];
            }
        },

        getIconHtml: function (id) {
            let iconHtml = this.getHelper().getAvatarHtml(id, 'small', 14, 'avatar-link');

            if (iconHtml) {
                iconHtml += ' ';
            }

            return iconHtml;
        },
    });
});
