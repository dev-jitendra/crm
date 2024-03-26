

define('views/admin/integrations/oauth2', ['views/admin/integrations/edit'], function (Dep) {

    return Dep.extend({

        template: 'admin/integrations/oauth2',

        data: function () {
            let redirectUri = this.redirectUri ||
                (this.getConfig().get('siteUrl') + '?entryPoint=oauthCallback');

            return _.extend({
                redirectUri: redirectUri,
            }, Dep.prototype.data.call(this));
        },
    });
});
