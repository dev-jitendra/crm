

define('views/admin/user-interface', ['views/settings/record/edit'], function (Dep) {

    return Dep.extend({

        layoutName: 'userInterface',

        saveAndContinueEditingAction: false,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.controlColorsField();
            this.listenTo(this.model, 'change:scopeColorsDisabled', this.controlColorsField, this);

            this.on('save', (initialAttributes) => {
                if (
                    this.model.get('theme') !== initialAttributes.theme ||
                    (this.model.get('themeParams').navbar || {}) !== (initialAttributes.themeParams).navbar
                ) {
                    this.setConfirmLeaveOut(false);

                    window.location.reload();
                }
            });
        },

        controlColorsField: function () {
            if (this.model.get('scopeColorsDisabled')) {
                this.hideField('tabColorsDisabled');
            } else {
                this.showField('tabColorsDisabled');
            }
        },
    });
});
