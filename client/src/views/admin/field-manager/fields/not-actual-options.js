

define('views/admin/field-manager/fields/not-actual-options', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.params.options = Espo.Utils.clone(this.model.get('options')) || [];

            this.listenTo(this.model, 'change:options', (m, v, o) => {
                this.params.options = Espo.Utils.clone(m.get('options')) || [];

                this.reRender();
            });
        },
    });
});
