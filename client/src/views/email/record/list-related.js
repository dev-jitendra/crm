

define('views/email/record/list-related', ['views/record/list'], function (Dep) {

    return Dep.extend({

        massActionList: ['remove', 'massUpdate'],
    });
});
