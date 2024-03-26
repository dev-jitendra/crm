

define('views/preferences/fields/tab-list', ['views/settings/fields/tab-list'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.params.options = this.params.options.filter(scope => {
                if (scope === '_delimiter_' || scope === 'Home') {
                    return true;
                }

                let defs = this.getMetadata().get(['scopes', scope]);

                if (!defs) {
                    return;
                }

                if (defs.disabled) {
                    return;
                }

                if (defs.acl) {
                    return this.getAcl().check(scope);
                }

                if (defs.tabAclPermission) {
                    let level = this.getAcl().get(defs.tabAclPermission);

                    return level && level !== 'no';
                }

                return true;
            });
        },
    });
});
