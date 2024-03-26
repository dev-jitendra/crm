

define('views/email-template/record/edit', ['views/record/edit', 'views/email-template/record/detail'], function (Dep, Detail) {

    return Dep.extend({

        saveAndContinueEditingAction: true,

        setup: function () {
            Dep.prototype.setup.call(this);
            Detail.prototype.listenToInsertField.call(this);
        },

    });
});
