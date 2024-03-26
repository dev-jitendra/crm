


define('views/admin/layouts/relationships', ['views/admin/layouts/rows'], function (Dep) {

    return Dep.extend({

        dataAttributeList: [
            'name',
            'dynamicLogicVisible',
            'style',
            'dynamicLogicStyled',
        ],

        editable: true,

        dataAttributesDefs: {
            style: {
                type: 'enum',
                options: [
                    'default',
                    'success',
                    'danger',
                    'warning',
                    'info',
                ],
                translation: 'LayoutManager.options.style',
            },
            dynamicLogicVisible: {
                type: 'base',
                view: 'views/admin/field-manager/fields/dynamic-logic-conditions',
                tooltip: 'dynamicLogicVisible',
            },
            dynamicLogicStyled: {
                type: 'base',
                view: 'views/admin/field-manager/fields/dynamic-logic-conditions',
                tooltip: 'dynamicLogicStyled',
            },
            name: {
                readOnly: true,
            },
        },

        languageCategory: 'links',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.dataAttributesDefs = Espo.Utils.cloneDeep(this.dataAttributesDefs);

            this.dataAttributesDefs.dynamicLogicVisible.scope = this.scope;
            this.dataAttributesDefs.dynamicLogicStyled.scope = this.scope;

            this.wait(true);

            this.loadLayout(() => {
                this.wait(false);
            });
        },

        loadLayout: function (callback) {
            this.getModelFactory().create(this.scope, (model) => {
                this.getHelper().layoutManager.getOriginal(this.scope, this.type, this.setId, (layout) => {

                    let allFields = [];

                    for (let field in model.defs.links) {
                        if (['hasMany', 'hasChildren'].indexOf(model.defs.links[field].type) !== -1) {
                            if (this.isLinkEnabled(model, field)) {
                                allFields.push(field);
                            }
                        }
                    }

                    allFields.sort((v1, v2) => {
                        return this.translate(v1, 'links', this.scope)
                            .localeCompare(this.translate(v2, 'links', this.scope));
                    });

                    allFields.push('_delimiter_');

                    this.enabledFieldsList = [];

                    this.enabledFields = [];
                    this.disabledFields = [];

                    for (let i in layout) {
                        let item = layout[i];
                        let o;

                        if (typeof item == 'string' || item instanceof String) {
                            o = {
                                name: item,

                                label: this.getLanguage().translate(item, 'links', this.scope)
                            };
                        }
                        else {
                            o = item;

                            o.label = this.getLanguage().translate(o.name, 'links', this.scope);
                        }

                        if (o.name[0] === '_') {
                            o.notEditable = true;

                            if (o.name === '_delimiter_') {
                                o.label = '. . .';
                            }
                        }

                        this.dataAttributeList.forEach(attribute => {
                            if (attribute === 'name') {
                                return;
                            }

                            if (attribute in o) {
                                return;
                            }

                            var value = this.getMetadata()
                                .get(['clientDefs', this.scope, 'relationshipPanels', o.name, attribute]);

                            if (value === null) {
                                return;
                            }

                            o[attribute] = value;
                        });

                        this.enabledFields.push(o);
                        this.enabledFieldsList.push(o.name);
                    }

                    for (let i in allFields) {
                        if (!_.contains(this.enabledFieldsList, allFields[i])) {
                            var name = allFields[i];

                            var label = this.getLanguage().translate(name, 'links', this.scope);

                            let o = {
                                name: name,
                                label: label,
                            };

                            if (o.name[0] === '_') {
                                o.notEditable = true;

                                if (o.name === '_delimiter_') {
                                    o.label = '. . .';
                                }
                            }

                            this.disabledFields.push(o);
                        }
                    }

                    this.rowLayout = this.enabledFields;

                    for (let i in this.rowLayout) {
                        let o = this.rowLayout[i];

                        o.label = this.getLanguage().translate(this.rowLayout[i].name, 'links', this.scope);

                        if (o.name === '_delimiter_') {
                            o.label = '. . .';
                        }

                        this.itemsData[this.rowLayout[i].name] = Espo.Utils.cloneDeep(this.rowLayout[i]);
                    }

                    callback();
                });
            });
        },

        validate: function () {
            return true;
        },

        isLinkEnabled: function (model, name) {
            return !model.getLinkParam(name, 'disabled') &&
                !model.getLinkParam(name, 'utility') &&
                !model.getLinkParam(name, 'layoutRelationshipsDisabled');
        },
    });
});
