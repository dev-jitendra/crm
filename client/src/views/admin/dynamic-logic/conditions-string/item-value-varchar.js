

define('views/admin/dynamic-logic/conditions-string/item-value-varchar',
['views/admin/dynamic-logic/conditions-string/item-base'], function (Dep) {

    return Dep.extend({

        template: 'admin/dynamic-logic/conditions-string/item-base',

        createValueFieldView: function () {
            var key = this.getValueViewKey();

            var viewName = 'views/fields/varchar';

            this.createView('value', viewName, {
                model: this.model,
                name: this.field,
                selector: '[data-view-key="'+key+'"]',
            });
        },
    });
});
