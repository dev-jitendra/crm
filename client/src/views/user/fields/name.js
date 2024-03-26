

define('views/user/fields/name', ['views/fields/person-name'], function (Dep) {

    return Dep.extend({

        listTemplate: 'user/fields/name/list-link',

        listLinkTemplate: 'user/fields/name/list-link',

        data: function () {
            return _.extend({
                avatar: this.getAvatarHtml(),
                frontScope: this.model.isPortal() ? 'PortalUser': 'User',
                isOwn: this.model.id === this.getUser().id,
            }, Dep.prototype.data.call(this));
        },

        getAvatarHtml: function () {
            return this.getHelper().getAvatarHtml(this.model.id, 'small', 16, 'avatar-link');
        },
    });
});
