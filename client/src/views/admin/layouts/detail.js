

define('views/admin/layouts/detail', ['views/admin/layouts/grid'], function (Dep) {

    return Dep.extend({

        dataAttributeList: [
            'name',
            'fullWidth',
            'customLabel',
            'noLabel',
        ],

        panelDataAttributeList: [
            'panelName',
            'dynamicLogicVisible',
            'style',
            'dynamicLogicStyled',
            'tabBreak',
            'tabLabel',
            'hidden',
        ],

        dataAttributesDefs: {
            fullWidth: {
                type: 'bool',
            },
            name: {
                readOnly: true,
            },
            label: {
                type: 'varchar',
                readOnly: true,
            },
            customLabel: {
                type: 'varchar',
                readOnly: true,
            },
            noLabel: {
                type: 'bool',
                readOnly: true,
            },
        },

        panelDataAttributesDefs: {
            panelName: {
                type: 'varchar',
            },
            style: {
                type: 'enum',
                options: [
                    'default',
                    'success',
                    'danger',
                    'warning',
                    'info',
                ],
                default: 'default',
                translation: 'LayoutManager.options.style',
                tooltip: 'panelStyle',
            },
            dynamicLogicVisible: {
                type: 'base',
                view: 'views/admin/field-manager/fields/dynamic-logic-conditions'
            },
            dynamicLogicStyled: {
                type: 'base',
                view: 'views/admin/field-manager/fields/dynamic-logic-conditions',
                tooltip: 'dynamicLogicStyled',
            },
            hidden: {
                type: 'bool',
                tooltip: 'hiddenPanel',
            },
            tabBreak: {
                type: 'bool',
                tooltip: 'tabBreak',
            },
            tabLabel: {
                type: 'varchar',
            },
        },

        defaultPanelFieldList: [
            'modifiedAt',
            'createdAt',
            'modifiedBy',
            'createdBy',
        ],

        panelDynamicLogicDefs: {
            fields: {
                tabLabel: {
                    visible: {
                        conditionGroup: [
                            {
                                attribute: 'tabBreak',
                                type: 'isTrue',
                            }
                        ]
                    }
                },
                dynamicLogicStyled: {
                    visible: {
                        conditionGroup: [
                            {
                                attribute: 'style',
                                type: 'notEquals',
                                value: 'default'
                            }
                        ]
                    }
                },
            }
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.panelDataAttributesDefs = Espo.Utils.cloneDeep(this.panelDataAttributesDefs);

            this.panelDataAttributesDefs.dynamicLogicVisible.scope = this.scope;
            this.panelDataAttributesDefs.dynamicLogicStyled.scope = this.scope;

            this.wait(true);

            this.loadLayout(() => {
                this.setupPanels();
                this.wait(false);
            });
        },

        loadLayout: function (callback) {
            let layout;
            let model;

            const promiseList = [];

            promiseList.push(
                new Promise(resolve => {
                    this.getModelFactory().create(this.scope, (m) => {
                        this.getHelper()
                            .layoutManager
                            .getOriginal(this.scope, this.type, this.setId, (layoutLoaded) => {
                                layout = layoutLoaded;
                                model = m;
                                resolve();
                            });
                    });
                })
            );

            if (['detail', 'detailSmall'].includes(this.type)) {
                promiseList.push(
                    new Promise(resolve => {
                        this.getHelper().layoutManager.getOriginal(
                            this.scope, 'sidePanels' + Espo.Utils.upperCaseFirst(this.type),
                            this.setId,
                            layoutLoaded => {
                                this.sidePanelsLayout = layoutLoaded;

                                resolve();
                            }
                        );
                    })
                );
            }

            promiseList.push(
                new Promise(resolve => {
                    if (this.getMetadata().get(['clientDefs', this.scope, 'layoutDefaultSidePanelDisabled'])) {
                        resolve();

                        return;
                    }

                    if (this.typeDefs.allFields) {
                        resolve();

                        return;
                    }

                    this.getHelper().layoutManager.getOriginal(
                        this.scope,
                        'defaultSidePanel',
                        this.setId,
                        layoutLoaded => {
                            this.defaultPanelFieldList = Espo.Utils.clone(this.defaultPanelFieldList);

                            layoutLoaded.forEach(item => {
                                let field = item.name;

                                if (!field) {
                                    return;
                                }

                                if (field === ':assignedUser') {
                                    field = 'assignedUser';
                                }

                                if (!this.defaultPanelFieldList.includes(field)) {
                                    this.defaultPanelFieldList.push(field);
                                }
                            });

                            resolve();
                        }
                    );
                })
            );

            Promise.all(promiseList).then(() => {
                this.readDataFromLayout(model, layout);

                if (callback) {
                    callback();
                }
            });
        },

        readDataFromLayout: function (model, layout) {
            const allFields = [];

            for (const field in model.defs.fields) {
                if (this.isFieldEnabled(model, field)) {
                    allFields.push(field);
                }
            }

            this.enabledFields = [];
            this.disabledFields = [];

            this.panels = layout;

            layout.forEach((panel) => {
                panel.rows.forEach((row) => {
                    row.forEach(cell => {
                        this.enabledFields.push(cell.name);
                    });
                });
            });

            allFields.sort((v1, v2) => {
                return this.translate(v1, 'fields', this.scope)
                    .localeCompare(this.translate(v2, 'fields', this.scope));
            });

            for (const i in allFields) {
                if (!_.contains(this.enabledFields, allFields[i])) {
                    this.disabledFields.push(allFields[i]);
                }
            }
        },

        isFieldEnabled: function (model, name) {
            if (this.hasDefaultPanel()) {
                if (this.defaultPanelFieldList.includes(name)) {
                    return false;
                }
            }

            const layoutList = model.getFieldParam(name, 'layoutAvailabilityList');

            let realType = this.realType;

            if (realType === 'detailSmall') {
                realType = 'detail';
            }

            if (
                layoutList &&
                !layoutList.includes(this.type) &&
                !layoutList.includes(realType)
            ) {
                return;
            }

            return !model.getFieldParam(name, 'disabled') &&
                !model.getFieldParam(name, 'utility') &&
                !model.getFieldParam(name, 'layoutDetailDisabled');
        },

        hasDefaultPanel: function () {
            if (this.getMetadata().get(['clientDefs', this.scope, 'defaultSidePanel', this.viewType]) === false) {
                return false;
            }

            if (this.getMetadata().get(['clientDefs', this.scope, 'defaultSidePanelDisabled'])) {
                return false;
            }

            if (this.sidePanelsLayout) {
                for (const name in this.sidePanelsLayout) {
                    if (name === 'default' && this.sidePanelsLayout[name].disabled) {
                        return false;
                    }
                }
            }

            return true;
        },

        validate: function (layout) {
            if (!Dep.prototype.validate.call(this, layout)) {
                return false;
            }

            const fieldList = [];

            layout.forEach(panel => {
                panel.rows.forEach(row => {
                    row.forEach(cell => {
                        if (cell !== false && cell !== null) {
                            if (cell.name) {
                                fieldList.push(cell.name);
                            }
                        }
                    });
                });
            });

            let incompatibleFieldList = [];

            let isIncompatible = false;

            fieldList.forEach(field => {
                if (isIncompatible) {
                    return;
                }

                const defs = this.getMetadata().get(['entityDefs', this.scope, 'fields', field]) || {};

                const targetFieldList = defs.detailLayoutIncompatibleFieldList || [];

                targetFieldList.forEach(itemField => {
                    if (isIncompatible) {
                        return;
                    }

                    if (~fieldList.indexOf(itemField)) {
                        isIncompatible = true;

                        incompatibleFieldList = [field].concat(targetFieldList);
                    }
                });
            });

            if (isIncompatible) {
                Espo.Ui.error(
                    this.translate('fieldsIncompatible', 'messages', 'LayoutManager')
                        .replace(
                            '{fields}',
                            incompatibleFieldList
                                .map(field => this.translate(field, 'fields', this.scope))
                                .join(', ')
                        )
                );

                return false;
            }

            return true;
        },
    });
});
