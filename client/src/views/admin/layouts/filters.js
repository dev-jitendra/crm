

define('views/admin/layouts/filters', ['views/admin/layouts/rows'], function (Dep) {

    return Dep.extend({

        dataAttributeList: ['name'],

        editable: false,

        ignoreList: [],

        setup: function () {
            Dep.prototype.setup.call(this);

            this.wait(true);

            this.loadLayout(() => {
                this.wait(false);
            });
        },

        loadLayout: function (callback) {
            this.getModelFactory().create(this.scope, (model) => {
                this.getHelper().layoutManager.getOriginal(this.scope, this.type, this.setId, (layout) => {

                    let allFields = [];

                    for (let field in model.defs.fields) {
                        if (
                            this.checkFieldType(model.getFieldParam(field, 'type')) &&
                            this.isFieldEnabled(model, field)
                        ) {
                            allFields.push(field);
                        }
                    }

                    allFields.sort((v1, v2) => {
                        return this.translate(v1, 'fields', this.scope)
                            .localeCompare(this.translate(v2, 'fields', this.scope));
                    });

                    this.enabledFieldsList = [];
                    this.enabledFields = [];
                    this.disabledFields = [];

                    for (let i in layout) {
                        this.enabledFields.push({
                            name: layout[i],
                            label: this.getLanguage().translate(layout[i], 'fields', this.scope)
                        });

                        this.enabledFieldsList.push(layout[i]);
                    }

                    for (let i in allFields) {
                        if (!_.contains(this.enabledFieldsList, allFields[i])) {
                            this.disabledFields.push({
                                name: allFields[i],
                                label: this.getLanguage().translate(allFields[i], 'fields', this.scope)
                            });
                        }
                    }

                    this.rowLayout = this.enabledFields;

                    for (let i in this.rowLayout) {
                        this.rowLayout[i].label = this.getLanguage().translate(this.rowLayout[i].name, 'fields', this.scope);
                    }

                    callback();
                });
            });
        },

        fetch: function () {
            var layout = [];

            $("#layout ul.enabled > li").each((i, el) => {
                layout.push($(el).data('name'));
            });

            return layout;
        },

        checkFieldType: function (type) {
            return this.getFieldManager().checkFilter(type);
        },

        validate: function () {
            return true;
        },

        isFieldEnabled: function (model, name) {
            if (this.ignoreList.indexOf(name) !== -1) {
                return false;
            }

            return !model.getFieldParam(name, 'disabled') &&
                !model.getFieldParam(name, 'utility') &&
                !model.getFieldParam(name, 'layoutFiltersDisabled');
        },
    });
});
