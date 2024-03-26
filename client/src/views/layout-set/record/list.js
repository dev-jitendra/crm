

define('views/layout-set/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        massActionList: [
            'remove',
            'export',
        ],
    });
});
