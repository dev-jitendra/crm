

define('views/admin/field-manager/fields/date/after-before', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            Dep.prototype.setupOptions.call(this);

            if (!this.model.scope) {
                return;
            }

            var list = this.getFieldManager().getEntityTypeFieldList(
                this.model.scope,
                {
                    typeList: ['date', 'datetime', 'datetimeOptional'],
                }
            );

            if (this.model.get('name')) {
                list = list.filter(function (item) {
                    return item !== this.model.get('name');
                }, this);
            }

            this.params.options = list;
        },

    });
});
