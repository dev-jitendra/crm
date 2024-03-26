

define('views/admin/field-manager/fields/phone/default', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.setOptionList(this.model.get('typeList') || ['']);

            this.listenTo(this.model, 'change:typeList', () => {
                this.setOptionList(this.model.get('typeList') || ['']);
            });
        }
    });
});
