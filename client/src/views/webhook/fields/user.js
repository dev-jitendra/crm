

define('views/webhook/fields/user', ['views/fields/link'], function (Dep) {

    return Dep.extend({

        selectPrimaryFilterName: 'activeApi',

    });
});
