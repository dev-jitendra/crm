

define('views/export/record/record', ['views/record/edit-for-modal'], function (Dep) {

    
    return Dep.extend({

        
        formatList: null,

        
        customParams: null,

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        setupBeforeFinal: function () {
            this.formatList = this.options.formatList;
            this.scope = this.options.scope;

            let fieldsData = this.getExportFieldsData();

            this.setupExportFieldDefs(fieldsData);
            this.setupExportLayout(fieldsData);
            this.setupExportDynamicLogic();

            this.controlFormatField();
            this.listenTo(this.model, 'change:format', () => this.controlFormatField());

            this.controlAllFields();
            this.listenTo(this.model, 'change:exportAllFields', () => this.controlAllFields());

            Dep.prototype.setupBeforeFinal.call(this);
        },

        setupExportFieldDefs: function (fieldsData) {
            let fieldDefs = {
                format: {
                    type: 'enum',
                    options: this.formatList,
                },
                fieldList: {
                    type: 'multiEnum',
                    options: fieldsData.list,
                    required: true,
                },
                exportAllFields: {
                    type: 'bool',
                },
            };

            this.customParams = {};

            this.formatList.forEach(format => {
                let fields = this.getFormatParamsDefs(format).fields || {};

                this.customParams[format] = [];

                for (let name in fields) {
                    let newName = this.modifyParamName(format, name);

                    this.customParams[format].push(name);

                    fieldDefs[newName] = Espo.Utils.cloneDeep(fields[name]);
                }
            });

            this.model.setDefs({fields: fieldDefs});
        },

        setupExportLayout: function (fieldsData) {
            this.detailLayout = [];

            let mainPanel = {
                rows: [
                    [
                        {name: 'format'},
                        false
                    ],
                    [
                        {name: 'exportAllFields'},
                        false
                    ],
                    [
                        {
                            name: 'fieldList',
                            options: {
                                translatedOptions: fieldsData.translations,
                            },
                        }
                    ],
                ]
            };

            this.detailLayout.push(mainPanel);

            this.formatList.forEach(format => {
                let rows = this.getFormatParamsDefs(format).layout || [];

                rows.forEach(row => {
                    row.forEach(item => {
                        item.name = this.modifyParamName(format, item.name);
                    });
                })

                this.detailLayout.push({
                    name: format,
                    rows: rows,
                })
            });
        },

        setupExportDynamicLogic: function () {
            this.dynamicLogicDefs = {
                fields: {},
            };

            this.formatList.forEach(format => {
                let defs = this.getFormatParamsDefs(format).dynamicLogic || {};

                this.customParams[format].forEach(param => {
                    let logic = defs[param] || {};

                    if (!logic.visible) {
                        logic.visible = {};
                    }

                    if (!logic.visible.conditionGroup) {
                        logic.visible.conditionGroup = [];
                    }

                    logic.visible.conditionGroup.push({
                        type: 'equals',
                        attribute: 'format',
                        value: format,
                    });

                    let newName = this.modifyParamName(format, param);

                    this.dynamicLogicDefs.fields[newName] = logic;
                });
            });
        },

        
        getFormatParamList: function (format) {
            return Object.keys(this.getFormatParamsDefs(format).fields || {});
        },

        
        getFormatParamsDefs: function (format) {
            let defs = this.getMetadata().get(['app', 'export', 'formatDefs', format]) || {};

            return Espo.Utils.cloneDeep(defs.params || {});
        },

        
        modifyParamName: function (format, name) {
            return format + Espo.Utils.upperCaseFirst(name);
        },

        
        getExportFieldsData: function () {
            let fieldList = this.getFieldManager().getEntityTypeFieldList(this.scope);
            let forbiddenFieldList = this.getAcl().getScopeForbiddenFieldList(this.scope);

            fieldList = fieldList.filter(item => {
                return !~forbiddenFieldList.indexOf(item);
            });

            fieldList = fieldList.filter(item => {
                let defs = this.getMetadata().get(['entityDefs', this.scope, 'fields', item]) || {};

                if (
                    defs.disabled ||
                    defs.exportDisabled ||
                    defs.type === 'map'
                ) {
                    return false
                }

                return true;
            });

            this.getLanguage().sortFieldList(this.scope, fieldList);

            fieldList.unshift('id');

            let fieldListTranslations = {};

            fieldList.forEach(item => {
                fieldListTranslations[item] = this.getLanguage().translate(item, 'fields', this.scope);
            });

            let setFieldList = this.model.get('fieldList') || [];

            setFieldList.forEach(item => {
                if (~fieldList.indexOf(item)) {
                    return;
                }

                if (!~item.indexOf('_')) {
                    return;
                }

                let arr = item.split('_');

                fieldList.push(item);

                let foreignScope = this.getMetadata().get(['entityDefs', this.scope, 'links', arr[0], 'entity']);

                if (!foreignScope) {
                    return;
                }

                fieldListTranslations[item] = this.getLanguage().translate(arr[0], 'links', this.scope) + '.' +
                    this.getLanguage().translate(arr[1], 'fields', foreignScope);
            });

            return {
                list: fieldList,
                translations: fieldListTranslations,
            };
        },

        controlAllFields: function () {
            if (!this.model.get('exportAllFields')) {
                this.showField('fieldList');

                return;
            }

            this.hideField('fieldList');
        },

        controlFormatField: function () {
            let format = this.model.get('format');

            this.formatList
                .filter(item => item !== format)
                .forEach(format => {
                    this.hidePanel(format);
                });

            this.formatList
                .filter(item => item === format)
                .forEach(format => {
                    this.customParams[format].length ?
                        this.showPanel(format) :
                        this.hidePanel(format);
                });
        },
    });
});
