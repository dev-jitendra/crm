

define('views/working-time-range/fields/users', ['views/fields/link-multiple'], function (Dep) {

    return Dep.extend({

        getSelectPrimaryFilterName: function () {
            return 'active';
        },
    });
});
