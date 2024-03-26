

define('views/settings/fields/group-tab-list', ['views/settings/fields/tab-list'], function (Dep) {

    return Dep.extend({

        noGroups: true,

        noDelimiters: true,
    });
});
