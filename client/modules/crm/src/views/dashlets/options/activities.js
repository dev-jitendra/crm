

define('crm:views/dashlets/options/activities', ['views/dashlets/options/base'], function (Dep) {

    return Dep.extend({

        init: function () {
            Dep.prototype.init.call(this);

            var entityTypeList = [];
            var activitiesEntityList = Espo.Utils.clone(this.getConfig().get('activitiesEntityList') || []);

            activitiesEntityList.push('Task');

            activitiesEntityList.forEach(item => {
                if (this.getMetadata().get(['scopes', item, 'disabled'])) {
                    return;
                }

                if (!this.getAcl().checkScope(item)) {
                    return;
                }

                entityTypeList.push(item);
            });

            this.fields.enabledScopeList.options = entityTypeList;
        },
    });
});
