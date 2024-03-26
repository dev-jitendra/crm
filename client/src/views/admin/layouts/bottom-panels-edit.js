

define('views/admin/layouts/bottom-panels-edit', ['views/admin/layouts/bottom-panels-detail'], function (Dep) {

    return Dep.extend({

        hasStream: false,

        hasRelationships: false,

        viewType: 'edit',
    });
});
