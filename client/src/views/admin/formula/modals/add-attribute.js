

define('views/admin/formula/modals/add-attribute', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        templateContent: '<div class="attribute" data-name="attribute">{{{attribute}}}</div>',

        backdrop: true,

        setup: function () {
            this.header = this.translate('Attribute');
            this.scope = this.options.scope;

            var model = new Model();

            this.createView('attribute', 'views/admin/formula/fields/attribute', {
                selector: '[data-name="attribute"]',
                model: model,
                mode: 'edit',
                scope: this.scope,
                defs: {
                    name: 'attribute',
                    params: {}
                },
                attributeList: this.options.attributeList,
            }, view => {
                this.listenTo(view, 'change', () => {
                    var list = model.get('attribute') || [];

                    if (!list.length) {
                        return;
                    }

                    this.trigger('add', list[0]);
                });
            });
        },

    });
});
