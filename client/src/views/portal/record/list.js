

define('views/portal/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        massActionList: [
            'remove',
        ],
    });
});
