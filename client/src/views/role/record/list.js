

define('views/role/record/list', ['views/record/list'], function (Dep) {

    return Dep.extend({

    	quickDetailDisabled: true,

        quickEditDisabled: true,

        massActionList: ['remove', 'export'],

        checkAllResultDisabled: true

    });
});
