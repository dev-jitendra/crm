

define('crm:views/admin/entity-manager/fields/status-list', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            let entityType = this.model.get('name');

            this.params.options = Espo.Utils.clone(
                this.getMetadata().get(['entityDefs', entityType, 'fields', 'status', 'options'])) || [];

            this.params.translation = `${entityType}.options.status`;
        },
    });
});
