



import View from 'view';




class SidePanelView extends View {

    template = 'record/panels/side'

    

    
    fieldList = null

    
    mode = 'detail'

    
    actionList

    
    buttonList

    
    readOnly = false

    
    inlineEditDisabled = false

    
    disabled = false

    
    defs

    events = {
        
        'click .action': function (e) {
            Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget, {
                actionItems: [...this.buttonList, ...this.actionList],
                className: 'panel-action',
            });
        },
    }

    data() {
        return {
            fieldList: this.getFieldList(),
            hiddenFields: this.recordHelper.getHiddenFields(),
        };
    }

    init() {
        this.panelName = this.options.panelName;
        this.defs = this.options.defs || {};
        this.recordHelper = this.options.recordHelper;

        if ('disabled' in this.options) {
            this.disabled = this.options.disabled;
        }

        this.buttonList = _.clone(this.defs.buttonList || this.buttonList || []);
        this.actionList = _.clone(this.defs.actionList || this.actionList || []);

        this.fieldList = this.options.fieldList || this.fieldList || this.defs.fieldList || [];

        this.mode = this.options.mode || this.mode;

        this.readOnlyLocked = this.options.readOnlyLocked || this.readOnly;
        this.readOnly = this.readOnly || this.options.readOnly;
        this.inlineEditDisabled = this.inlineEditDisabled || this.options.inlineEditDisabled;

        this.recordViewObject = this.options.recordViewObject;
    }

    setup() {
        this.setupFields();

        this.fieldList = this.fieldList.map(d => {
            let item = d;

            if (typeof item !== 'object') {
                item = {
                    name: item,
                    viewKey: item + 'Field'
                };
            }

            item = Espo.Utils.clone(item);

            item.viewKey = item.name + 'Field';
            item.label = item.label || item.name;

            if (this.recordHelper.getFieldStateParam(item.name, 'hidden') !== null) {
                item.hidden = this.recordHelper.getFieldStateParam(item.name, 'hidden');
            } else {
                this.recordHelper.setFieldStateParam(item.name, 'hidden', item.hidden || false);
            }

            return item;
        });

        this.fieldList = this.fieldList.filter((item) => {
            if (!item.name) {
                return;
            }

            if (!item.isAdditional) {
                if (!(item.name in (((this.model.defs || {}).fields) || {}))) return;
            }

            return true;
        });

        this.createFields();
    }

    afterRender() {
        if (this.$el.children().length === 0) {
            this.$el.parent().addClass('hidden');
        }
    }

    
    setupFields() {}

    
    createField(field, viewName, params, mode, readOnly, options) {
        const type = this.model.getFieldType(field) || 'base';

        viewName = viewName ||
            this.model.getFieldParam(field, 'view') ||
            this.getFieldManager().getViewName(type);

        const o = {
            model: this.model,
            selector: '.field[data-name="' + field + '"]',
            defs: {
                name: field,
                params: params || {},
            },
            mode: mode || this.mode,
            dataObject: this.options.dataObject,
        };

        if (options) {
            for (const param in options) {
                o[param] = options[param];
            }
        }

        let readOnlyLocked = this.readOnlyLocked;

        if (this.readOnly) {
            o.readOnly = true;
        }
        else {
            if (readOnly !== null) {
                o.readOnly = readOnly;
            }
        }

        if (readOnly) {
            readOnlyLocked = true;
        }

        if (this.inlineEditDisabled) {
            o.inlineEditDisabled = true;
        }

        if (this.recordHelper.getFieldStateParam(field, 'hidden')) {
            o.disabled = true;
        }

        if (this.recordHelper.getFieldStateParam(field, 'hiddenLocked')) {
            o.disabledLocked = true;
        }

        if (this.recordHelper.getFieldStateParam(field, 'readOnly')) {
            o.readOnly = true;
        }

        if (this.recordHelper.getFieldStateParam(field, 'required') !== null) {
            o.defs.params.required = this.recordHelper.getFieldStateParam(field, 'required');
        }

        if (!readOnlyLocked && this.recordHelper.getFieldStateParam(field, 'readOnlyLocked')) {
            readOnlyLocked = true;
        }

        if (readOnlyLocked) {
            o.readOnlyLocked = readOnlyLocked;
        }

        if (this.recordHelper.hasFieldOptionList(field)) {
            o.customOptionList = this.recordHelper.getFieldOptionList(field);
        }

        if (this.recordViewObject) {
            o.validateCallback = () => this.recordViewObject.validateField(field);
        }

        o.recordHelper = this.recordHelper;

        const viewKey = field + 'Field';

        this.createView(viewKey, viewName, o);
    }

    
    createFields() {
        this.getFieldList().forEach(item => {
            let view = null;
            let field;
            let readOnly = null;

            if (typeof item === 'object') {
                field = item.name;
                view = item.view;

                if ('readOnly' in item) {
                    readOnly = item.readOnly;
                }
            }
            else {
               field = item;
            }

            if (!item.isAdditional && !(field in this.model.defs.fields)) {
                return;
            }

            readOnly = readOnly || false;

            this.createField(field, view, null, null, readOnly, item.options);
        });
    }

    
    
    getFields() {
        return this.getFieldViews();
    }

    
    getFieldViews() {
        const fields = {};

        this.getFieldList().forEach(item => {
            if (this.hasView(item.viewKey)) {
                fields[item.name] = this.getView(item.viewKey);
            }
        });

        return fields;
    }

    
    getFieldList() {
        return this.fieldList.map(item => {
            if (typeof item !== 'object') {
                return {
                    name: item,
                };
            }

            return item;
        });
    }

    
    getActionList() {
        return this.actionList || [];
    }

    
    getButtonList() {
        return this.buttonList || [];
    }

    
    actionRefresh() {
        this.model.fetch();
    }

    
    
    isTabHidden() {
        if (this.defs.tabNumber === -1 || typeof this.defs.tabNumber === 'undefined') {
            return false;
        }

        const parentView = this.getParentView();

        if (!parentView) {
            return this.defs.tabNumber > 0;
        }

        
        if (parentView && parentView.hasTabs) {
            return parentView.currentTab !== defs.tabNumber;
        }

        return false;
    }
}

export default SidePanelView;
