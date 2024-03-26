

define('views/admin/field-manager/header', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/field-manager/header',

        data: function () {
            return {
                scope: this.scope,
                field: this.field,
            };
        },

        setup: function () {
            this.scope = this.options.scope;
            this.field = this.options.field;
        },

        setField: function (field) {
            this.field = field;

            if (this.isRendered()) {
                this.reRender();
            }
        },
    });
});
