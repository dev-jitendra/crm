

define('views/template/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        saveAndContinueEditingAction: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.hideField('variables');

            this.on('after:set-edit-mode', function () {
                this.showField('variables');
            }, this);

            this.on('after:set-detail-mode', function () {
                this.hideField('variables');
            }, this);
        },

    });
});
