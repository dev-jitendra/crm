

define('views/admin/layouts/modals/panel-attributes', ['views/modal', 'model'], function (Dep, Model) {

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
                    text: 'Cancel',
                },
            ];

            let model = new Model();

            model.name = 'LayoutManager';
            model.set(this.options.attributes || {});

            let attributeList = this.options.attributeList;
            let attributeDefs = this.options.attributeDefs;

            this.createView('edit', 'views/admin/layouts/record/edit-attributes', {
                selector: '.edit-container',
                attributeList: attributeList,
                attributeDefs: attributeDefs,
                model: model,
                dynamicLogicDefs: this.options.dynamicLogicDefs,
            });
        },

        actionSave: function () {
            let editView = this.getView('edit');
            let attrs = editView.fetch();

            editView.model.set(attrs, {silent: true});

            if (editView.validate()) {
                return;
            }

            let attributes = editView.model.attributes;

            this.trigger('after:save', attributes);

            return true;
        },
    });
});
