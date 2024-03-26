

define('views/admin/layouts/modals/edit-attributes', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        templateContent: `
            <div class="panel panel-default no-side-margin">
                <div class="panel-body">
                    <div class="edit-container">{{{edit}}}</div>
                </div>
            </div>
        `,

        className: 'dialog dialog-record',

        shortcutKeys: {
            'Control+Enter': function (e) {
                this.actionSave();

                e.preventDefault();
                e.stopPropagation();
            },
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'save',
                    text: this.translate('Apply'),
                    style: 'primary',
                },
                {
                    name: 'cancel',
                    text: this.translate('Cancel'),
                },
            ];

            const model = new Model();

            model.name = 'LayoutManager';

            model.set(this.options.attributes || {});

            this.headerText = null;

            if (this.options.languageCategory) {
                this.headerText = this.translate(
                    this.options.name,
                    this.options.languageCategory,
                    this.options.scope
                );
            }

            let attributeList = Espo.Utils.clone(this.options.attributeList || []);

            const filteredAttributeList = [];

            attributeList.forEach(item => {
                const defs = this.options.attributeDefs[item] || {};

                if (defs.readOnly || defs.hidden) {
                    return;
                }

                filteredAttributeList.push(item);
            });

            attributeList = filteredAttributeList;

            this.createView('edit', 'views/admin/layouts/record/edit-attributes', {
                selector: '.edit-container',
                attributeList: attributeList,
                attributeDefs: this.options.attributeDefs,
                dynamicLogicDefs: this.options.dynamicLogicDefs,
                model: model,
            });
        },

        actionSave: function () {
            const editView = this.getView('edit');

            const attrs = editView.fetch();

            editView.model.set(attrs, {silent: true});

            if (editView.validate()) {
                return;
            }

            const attributes = editView.model.attributes;

            this.trigger('after:save', attributes);

            return true;
        },
    });
});
