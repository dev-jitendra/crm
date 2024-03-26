

define('views/attachment/fields/parent', ['views/fields/link-parent'], function (Dep) {

    return Dep.extend({

        ignoreScopeList: [
            'Preferences',
            'ExternalAccount',
            'Notification',
            'Note',
            'ArrayValue',
            'Attachment',
        ],

        displayEntityType: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.foreignScopeList = this.getMetadata().getScopeEntityList().filter(item => {
                if (!this.getUser().isAdmin()) {
                    if (!this.getAcl().checkScopeHasAcl(item)) {
                        return;
                    }
                }

                if (~this.ignoreScopeList.indexOf(item)) {
                    return;
                }

                if (!this.getAcl().checkScope(item)) {
                    return;
                }

                return true;
            });

            this.getLanguage().sortEntityList(this.foreignScopeList);

            this.foreignScope = this.model.get(this.typeName) || this.foreignScopeList[0];
        },
    });
});
