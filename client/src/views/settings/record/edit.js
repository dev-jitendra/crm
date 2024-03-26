

define('views/settings/record/edit', ['views/record/edit'], function (Dep) {

    return Dep.extend({

        saveAndContinueEditingAction: false,

        sideView: null,

        layoutName: 'settings',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'after:save', () => {
                this.getConfig().set(this.model.getClonedAttributes());
            });
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);
        },

        exit: function (after) {
            if (after === 'cancel') {
                this.getRouter().navigate('#Admin', {trigger: true});
            }
        },
    });
});

