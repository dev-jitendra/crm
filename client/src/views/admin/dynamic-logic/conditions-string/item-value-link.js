

define('views/admin/dynamic-logic/conditions-string/item-value-link',
['views/admin/dynamic-logic/conditions-string/item-base'], function (Dep) {

    return Dep.extend({

        template: 'admin/dynamic-logic/conditions-string/item-base',

        createValueFieldView: function () {
            const key = this.getValueViewKey();

            const viewName = 'views/fields/link';

            this.createView('value', viewName, {
                model: this.model,
                name: 'link',
                selector: '[data-view-key="' + key + '"]',
                foreignScope: this.getMetadata().get(['entityDefs', this.scope, 'fields', this.field, 'entity']) ||
                    this.getMetadata().get(['entityDefs', this.scope, 'links', this.field, 'entity'])
            });
        },
    });
});
