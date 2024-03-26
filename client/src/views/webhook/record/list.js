

define('views/webhook/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        massActionList: ['remove', 'massUpdate', 'export'],

    });
});
