

define('views/admin/dynamic-logic/modals/add-field', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        templateContent: `<div class="field" data-name="field">{{{field}}}</div>`,

        events: {
            'click a[data-action="addField"]': function (e) {
                this.trigger('add-field', $(e.currentTarget).data().name);
            },
        },

        setup: function () {
            this.header = this.translate('Add Field');
            this.scope = this.options.scope;

            const model = new Model();

            this.createView('field', 'views/admin/dynamic-logic/fields/field', {
                selector: '[data-name="field"]',
                model: model,
                mode: 'edit',
                scope: this.scope,
                defs: {
                    name: 'field',
                    params: {},
                },
            }, (view) => {
                this.listenTo(view, 'change', () => {
                    const list = model.get('field') || [];

                    if (!list.length) {
                        return;
                    }

                    this.trigger('add-field', list[0]);
                });
            });
        },
    });
});
