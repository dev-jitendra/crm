

define('views/last-viewed/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

        layoutName: 'listForLastViewed',

        rowActionsDisabled: true,
        massActionsDisabled: true,
        headerDisabled: true,
    });
});
