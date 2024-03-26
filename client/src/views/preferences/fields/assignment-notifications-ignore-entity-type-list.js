

define('views/preferences/fields/assignment-notifications-ignore-entity-type-list',
['views/fields/checklist'], function (Dep) {

    return Dep.extend({

        isInversed: true,

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(this.getConfig().get('assignmentNotificationsEntityList')) || [];
        },
    });
});
