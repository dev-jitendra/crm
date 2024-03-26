

define('views/authentication-provider/record/detail', ['views/record/detail', 'helpers/misc/authentication-provider'],
function (Dep, Helper) {

    return Dep.extend({

        editModeDisabled: true,

        
        helper: null,

        setup: function () {
            this.helper = new Helper(this);

            Dep.prototype.setup.call(this);
        },

        setupBeforeFinal: function () {
            this.dynamicLogicDefs = this.helper.setupMethods();

            Dep.prototype.setupBeforeFinal.call(this);

            this.helper.setupPanelsVisibility(() => {
                this.processDynamicLogic();
            });
        },

        modifyDetailLayout: function (layout) {
            this.helper.modifyDetailLayout(layout);
        },
    });
});
