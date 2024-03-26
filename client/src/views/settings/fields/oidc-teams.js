

define('views/settings/fields/oidc-teams', ['views/fields/link-multiple-with-role'], function (Dep) {

    return Dep.extend({

        forceRoles: true,

        roleType: 'varchar',

        columnName: 'group',

        roleMaxLength: 255,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.rolePlaceholderText = this.translate('IdP Group', 'labels', 'Settings');
        },
    });
});
