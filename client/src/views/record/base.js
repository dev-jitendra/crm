



import View from 'view';
import ViewRecordHelper from 'view-record-helper';
import DynamicLogic from 'dynamic-logic';
import _ from 'underscore';
import $ from 'jquery';
import DefaultsPopulator from 'helpers/model/defaults-populator';


class BaseRecordView extends View {

    
    type = 'edit'

    
    entityType = null

    
    scope = null

    
    isNew = false

    
    dependencyDefs = {}

    
    dynamicLogicDefs = {}

    
    fieldList = null

    
    mode = null

    
    lastSaveCancelReason = null

    
    recordHelper = null

    
    MODE_DETAIL = 'detail'
    
    MODE_EDIT = 'edit'

    
    TYPE_DETAIL = 'detail'
    
    
    TYPE_EDIT = 'edit'

    
    hideField(name, locked) {
        this.recordHelper.setFieldStateParam(name, 'hidden', true);

        if (locked) {
            this.recordHelper.setFieldStateParam(name, 'hiddenLocked', true);
        }

        const processHtml = () => {
            const fieldView = this.getFieldView(name);

            if (fieldView) {
                const $field = fieldView.$el;
                const $cell = $field.closest('.cell[data-name="' + name + '"]');
                const $label = $cell.find('label.control-label[data-name="' + name + '"]');

                $field.addClass('hidden');
                $label.addClass('hidden');
                $cell.addClass('hidden-cell');
            } else {
                this.$el.find('.cell[data-name="' + name + '"]').addClass('hidden-cell');
                this.$el.find('.field[data-name="' + name + '"]').addClass('hidden');
                this.$el.find('label.control-label[data-name="' + name + '"]').addClass('hidden');
            }
        };

        if (this.isRendered()) {
            processHtml();
        }
        else {
            this.once('after:render', () => {
                processHtml();
            });
        }

        const view = this.getFieldView(name);

        if (view) {
            view.setDisabled(locked);
        }
    }

    
    showField(name) {
        if (this.recordHelper.getFieldStateParam(name, 'hiddenLocked')) {
            return;
        }

        this.recordHelper.setFieldStateParam(name, 'hidden', false);

        const processHtml = () => {
            const fieldView = this.getFieldView(name);

            if (fieldView) {
                const $field = fieldView.$el;
                const $cell = $field.closest('.cell[data-name="' + name + '"]');
                const $label = $cell.find('label.control-label[data-name="' + name + '"]');

                $field.removeClass('hidden');
                $label.removeClass('hidden');
                $cell.removeClass('hidden-cell');

                return;
            }

            this.$el.find('.cell[data-name="' + name + '"]').removeClass('hidden-cell');
            this.$el.find('.field[data-name="' + name + '"]').removeClass('hidden');
            this.$el.find('label.control-label[data-name="' + name + '"]').removeClass('hidden');
        };

        if (this.isRendered()) {
            processHtml();
        }
        else {
            this.once('after:render', () => {
                processHtml();
            });
        }

        const view = this.getFieldView(name);

        if (view) {
            if (!view.disabledLocked) {
                view.setNotDisabled();
            }
        }
    }

    
    setFieldReadOnly(name, locked) {
        const previousValue = this.recordHelper.getFieldStateParam(name, 'readOnly');

        this.recordHelper.setFieldStateParam(name, 'readOnly', true);

        if (locked) {
            this.recordHelper.setFieldStateParam(name, 'readOnlyLocked', true);
        }

        const view = this.getFieldView(name);

        if (view) {
            view.setReadOnly(locked)
                .catch(() => {});
        }

        if (!previousValue) {
            this.trigger('set-field-read-only', name);
        }

        

        if (!view && !this.isReady) {
            this.once('ready', () => {
                const view = this.getFieldView(name);

                if (
                    view &&
                    !view.readOnly &&
                    this.recordHelper.getFieldStateParam(name, 'readOnly')
                ) {
                    view.setReadOnly(locked);
                }
            })
        }
    }

    
    setFieldNotReadOnly(name) {
        const previousValue = this.recordHelper.getFieldStateParam(name, 'readOnly');

        this.recordHelper.setFieldStateParam(name, 'readOnly', false);

        const view = this.getFieldView(name);

        if (view && view.readOnly) {
            view.setNotReadOnly();

            if (this.mode === this.MODE_EDIT) {
                if (!view.readOnlyLocked && view.isDetailMode()) {
                    view.setEditMode()
                        .then(() => view.reRender());
                }
            }
        }

        if (previousValue) {
            this.trigger('set-field-not-read-only', name);
        }

        if (!view && !this.isReady) {
            this.once('ready', () => {
                const view = this.getFieldView(name);

                if (
                    view &&
                    view.readOnly &&
                    !this.recordHelper.getFieldStateParam(name, 'readOnly')
                ) {
                    view.setNotReadOnly();
                }
            })
        }
    }

    
    setFieldRequired(name) {
        const previousValue = this.recordHelper.getFieldStateParam(name, 'required');

        this.recordHelper.setFieldStateParam(name, 'required', true);

        const view = this.getFieldView(name);

        if (view) {
            view.setRequired();
        }

        if (!previousValue) {
            this.trigger('set-field-required', name);
        }
    }

    
    setFieldNotRequired(name) {
        const previousValue = this.recordHelper.getFieldStateParam(name, 'required');

        this.recordHelper.setFieldStateParam(name, 'required', false);

        const view = this.getFieldView(name);

        if (view) {
            view.setNotRequired();
        }

        if (previousValue) {
            this.trigger('set-field-not-required', name);
        }
    }

    
    setFieldOptionList(name, list) {
        const had = this.recordHelper.hasFieldOptionList(name);
        const previousList = this.recordHelper.getFieldOptionList(name);

        this.recordHelper.setFieldOptionList(name, list);

        const view = this.getFieldView(name);

        if (view) {
            if ('setOptionList' in view) {
                view.setOptionList(list);
            }
        }

        if (!had || !_(previousList).isEqual(list)) {
            this.trigger('set-field-option-list', name, list);
        }
    }

    
    resetFieldOptionList(name) {
        const had = this.recordHelper.hasFieldOptionList(name);

        this.recordHelper.clearFieldOptionList(name);

        const view = this.getFieldView(name);

        if (view) {
            if ('resetOptionList' in view) {
                view.resetOptionList();
            }
        }

        if (had) {
            this.trigger('reset-field-option-list', name);
        }
    }

    
    showPanel(name, softLockedType) {
        this.recordHelper.setPanelStateParam(name, 'hidden', false);

        if (this.isRendered()) {
            this.$el.find('.panel[data-name="'+name+'"]').removeClass('hidden');
        }
    }

    
    hidePanel(name, locked, softLockedType) {
        this.recordHelper.setPanelStateParam(name, 'hidden', true);

        if (this.isRendered()) {
            this.$el.find('.panel[data-name="'+name+'"]').addClass('hidden');
        }
    }

    
    stylePanel(name) {
        this.recordHelper.setPanelStateParam(name, 'styled', true);

        const process = () => {
            const $panel = this.$el.find('.panel[data-name="' + name + '"]');
            const $btn = $panel.find('> .panel-heading .btn');

            const style = $panel.attr('data-style');

            if (!style) {
                return;
            }

            $panel.removeClass('panel-default');
            $panel.addClass('panel-' + style);

            $btn.removeClass('btn-default');
            $btn.addClass('btn-' + style);
        };

        if (this.isRendered()) {
            process();

            return;
        }

        this.once('after:render', () => {
            process();
        });
    }

    
    unstylePanel(name) {
        this.recordHelper.setPanelStateParam(name, 'styled', false);

        const process = () => {
            const $panel = this.$el.find('.panel[data-name="' + name + '"]');
            const $btn = $panel.find('> .panel-heading .btn');

            const style = $panel.attr('data-style');

            if (!style) {
                return;
            }

            $panel.removeClass('panel-' + style);
            $panel.addClass('panel-default');

            $btn.removeClass('btn-' + style);
            $btn.addClass('btn-default');
        };

        if (this.isRendered()) {
            process();

            return;
        }

        this.once('after:render', () => {
            process();
        });
    }

    
    setConfirmLeaveOut(value) {
        if (!this.getRouter()) {
            return;
        }

        this.getRouter().confirmLeaveOut = value;
    }

    
    getFieldViews(withHidden) {
        const fields = {};

        this.fieldList.forEach(item => {
            const view = this.getFieldView(item);

            if (view) {
                fields[item] = view;
            }
        });

        return fields;
    }

    
    getFields() {
        return this.getFieldViews();
    }

    
    getFieldView(name) {
        
        let view =  this.getView(name + 'Field') || null;

        
        if (!view) {
            view = this.getView(name) || null;
        }

        return view;
    }

    
    getField(name) {
        return this.getFieldView(name);
    }

    
    getFieldList() {
        return Object.keys(this.getFieldViews());
    }

    
    getFieldViewList() {
        return this.getFieldList()
            .map(field => this.getFieldView(field))
            .filter(view => view !== null);
    }

    
    data() {
        return {
            scope: this.scope,
            entityType: this.entityType,
            hiddenPanels: this.recordHelper.getHiddenPanels(),
            hiddenFields: this.recordHelper.getHiddenFields(),
        };
    }

    
    handleDataBeforeRender(data) {
        this.getFieldList().forEach((field) => {
            const viewKey = field + 'Field';

            data[field] = data[viewKey];
        });
    }

    
    setup() {
        if (typeof this.model === 'undefined') {
            throw new Error('Model has not been injected into record view.');
        }

        
        this.recordHelper = this.options.recordHelper || new ViewRecordHelper();

        this.dynamicLogicDefs = this.options.dynamicLogicDefs || this.dynamicLogicDefs;

        this.on('remove', () => {
            if (this.isChanged) {
                this.resetModelChanges();
            }

            this.setIsNotChanged();
        });

        this.entityType = this.model.entityType || this.model.name || 'Common';
        this.scope = this.options.scope || this.entityType;

        this.fieldList = this.options.fieldList || this.fieldList || [];

        this.numId = Math.floor((Math.random() * 10000) + 1);

        this.id = Espo.Utils.toDom(this.entityType) + '-' +
            Espo.Utils.toDom(this.type) + '-' + this.numId;

        if (this.model.isNew()) {
            this.isNew = true;
        }

        this.setupBeforeFinal();
    }

    
    setupBeforeFinal() {
        this.attributes = this.model.getClonedAttributes();

        this.listenTo(this.model, 'change', (m, o) => {
            if (o.sync) {
                for (const attribute in m.attributes) {
                    if (!m.hasChanged(attribute)) {
                        continue;
                    }

                    this.attributes[attribute] = Espo.Utils.cloneDeep(
                        m.get(attribute)
                    );
                }

                return;
            }

            if (this.mode === this.MODE_EDIT) {
                this.setIsChanged();
            }
        });

        if (this.options.attributes) {
            this.model.set(this.options.attributes);
        }

        this.listenTo(this.model, 'sync', () => {
             this.attributes = this.model.getClonedAttributes();
        });

        this.initDependency();
        this.initDynamicLogic();
    }

    
    setInitialAttributeValue(attribute, value) {
        this.attributes[attribute] = value;
    }

    
    
    checkAttributeIsChanged(name) {
        return !_.isEqual(this.attributes[name], this.model.get(name));
    }

    
    resetModelChanges() {
        if (this.updatedAttributes) {
            this.attributes = this.updatedAttributes;

            this.updatedAttributes = null;
        }

        const attributes = this.model.attributes;

        for (const attr in attributes) {
            if (!(attr in this.attributes)) {
                this.model.unset(attr);
            }
        }

        this.model.set(this.attributes, {skipReRender: true});
    }

    
    setModelAttributes(setAttributes, options) {
        for (const item in this.model.attributes) {
            if (!(item in setAttributes)) {
                this.model.unset(item);
            }
        }

        this.model.set(setAttributes, options || {});
    }

    
    initDynamicLogic() {
        this.dynamicLogicDefs = Espo.Utils.clone(this.dynamicLogicDefs || {});
        this.dynamicLogicDefs.fields = Espo.Utils.clone(this.dynamicLogicDefs.fields);
        this.dynamicLogicDefs.panels = Espo.Utils.clone(this.dynamicLogicDefs.panels);

        this.dynamicLogic = new DynamicLogic(this.dynamicLogicDefs, this);

        this.listenTo(this.model, 'change', () => this.processDynamicLogic());
        this.processDynamicLogic();
    }

    
    processDynamicLogic() {
        this.dynamicLogic.process();
    }

    
    initDependency() {
        
        Object.keys(this.dependencyDefs || {}).forEach((attr) => {
            this.listenTo(this.model, 'change:' + attr, () => {
                this._handleDependencyAttribute(attr);
            });
        });

        this._handleDependencyAttributes();
    }

    
    initDependancy() {
        this.initDependency();
    }

    
    setupFieldLevelSecurity() {
        const forbiddenFieldList = this.getAcl().getScopeForbiddenFieldList(this.entityType, 'read');

        forbiddenFieldList.forEach((field) => {
            this.hideField(field, true);
        });

        const readOnlyFieldList = this.getAcl().getScopeForbiddenFieldList(this.entityType, 'edit');

        readOnlyFieldList.forEach((field) => {
            this.setFieldReadOnly(field, true);
        });
    }

    
    setIsChanged() {
        this.isChanged = true;
    }

    
    setIsNotChanged() {
        this.isChanged = false;
    }

    
    validate() {
        const invalidFieldList = [];

        this.getFieldList().forEach(field => {
            const fieldIsInvalid = this.validateField(field);

            if (fieldIsInvalid) {
                invalidFieldList.push(field)
            }
        });

        if (!!invalidFieldList.length) {
            this.onInvalid(invalidFieldList);
        }

        return !!invalidFieldList.length;
    }

    
    onInvalid(invalidFieldList) {}

    
    validateField(field) {
        const msg =
            this.translate('fieldInvalid', 'messages')
                .replace('{field}', this.translate(field, 'fields', this.entityType));
        const fieldView = this.getFieldView(field);

        if (!fieldView) {
            return false;
        }

        let notValid = false;

        if (
            fieldView.isEditMode() &&
            !fieldView.disabled &&
            !fieldView.readOnly
        ) {
            notValid = fieldView.validate() || notValid;
        }

        if (notValid) {
            if (fieldView.$el) {
                const rect = fieldView.$el.get(0).getBoundingClientRect();

                if (
                    rect.top === 0 &&
                    rect.bottom === 0 &&
                    rect.left === 0 &&
                    fieldView.$el.closest('.panel.hidden').length
                ) {
                    setTimeout(() => {
                        const msg = this.translate('Not valid') + ': ' +
                            (
                                fieldView.lastValidationMessage ||
                                this.translate(field, 'fields', this.entityType)
                            );

                        Espo.Ui.error(msg, true);
                    }, 10);
                }
            }

            return true;
        }

        if (
            this.dynamicLogic &&
            this.dynamicLogicDefs &&
            this.dynamicLogicDefs.fields &&
            this.dynamicLogicDefs.fields[field] &&
            this.dynamicLogicDefs.fields[field].invalid &&
            this.dynamicLogicDefs.fields[field].invalid.conditionGroup
        ) {
            const invalidConditionGroup = this.dynamicLogicDefs.fields[field].invalid.conditionGroup;

            const fieldInvalid = this.dynamicLogic.checkConditionGroup(invalidConditionGroup);

            notValid = fieldInvalid || notValid;

            if (fieldInvalid) {

                fieldView.showValidationMessage(msg);

                fieldView.trigger('invalid');
            }
        }

        return notValid;
    }

    
    afterSave() {
        if (this.isNew) {
            Espo.Ui.success(this.translate('Created'));
        }
        else {
            Espo.Ui.success(this.translate('Saved'));
        }

        this.setIsNotChanged();
    }

    
    beforeBeforeSave() {}

    
    beforeSave() {
        Espo.Ui.notify(this.translate('saving', 'messages'));
    }

    
    afterSaveError() {}

    
    afterNotModified() {
        Espo.Ui.warning(this.translate('notModified', 'messages'));

        this.setIsNotChanged();
    }

    
    afterNotValid() {
        Espo.Ui.error(this.translate('Not valid'));
    }

    

    
    save(options) {
        options = options || {};

        const headers = options.headers || {};

        const model = this.model;

        this.lastSaveCancelReason = null;

        this.beforeBeforeSave();

        const fetchedAttributes = this.fetch();
        const initialAttributes = this.attributes;
        const beforeSaveAttributes = this.model.getClonedAttributes();

        const attributes = _.extend(
            Espo.Utils.cloneDeep(beforeSaveAttributes),
            fetchedAttributes
        );

        let setAttributes = {};

        if (model.isNew()) {
            setAttributes = attributes;
        }

        if (!model.isNew()) {
            for (const attr in attributes) {
                if (_.isEqual(initialAttributes[attr], attributes[attr])) {
                    continue;
                }

                setAttributes[attr] = attributes[attr];
            }

            const forcePatchAttributeDependencyMap = this.forcePatchAttributeDependencyMap || {};

            for (const attr in forcePatchAttributeDependencyMap) {
                if (attr in setAttributes) {
                    continue;
                }

                if (!(attr in fetchedAttributes)) {
                    continue;
                }

                const depAttributeList = forcePatchAttributeDependencyMap[attr];

                const treatAsChanged = !!depAttributeList.find(attr => attr in setAttributes);

                if (treatAsChanged) {
                    setAttributes[attr] = attributes[attr];
                }
            }
        }

        if (Object.keys(setAttributes).length === 0) {
            if (!options.skipNotModifiedWarning) {
                this.afterNotModified();
            }

            this.lastSaveCancelReason = 'notModified';

            this.trigger('cancel:save', {reason: 'notModified'});

            return Promise.reject('notModified');
        }

        model.set(setAttributes, {silent: true});

        if (this.validate()) {
            model.attributes = beforeSaveAttributes;

            this.afterNotValid();

            this.lastSaveCancelReason = 'invalid';

            this.trigger('cancel:save', {reason: 'invalid'});

            return Promise.reject('invalid');
        }

        if (options.afterValidate) {
            options.afterValidate();
        }

        const optimisticConcurrencyControl = this.getMetadata()
            .get(['entityDefs', this.entityType, 'optimisticConcurrencyControl']);

        if (optimisticConcurrencyControl && this.model.get('versionNumber') !== null) {
            headers['X-Version-Number'] = this.model.get('versionNumber');
        }

        if (this.model.isNew() && this.options.duplicateSourceId) {
            headers['X-Duplicate-Source-Id'] = this.options.duplicateSourceId;
        }

        this.beforeSave();

        this.trigger('before:save');
        model.trigger('before:save');

        const onError = (xhr, reject, resolve) => {
            this.handleSaveError(xhr, options, resolve, reject)
                .then(skipReject => {
                    if (skipReject) {
                        return;
                    }

                    reject('error');
                });

            this.afterSaveError();
            this.setModelAttributes(beforeSaveAttributes);

            this.lastSaveCancelReason = 'error';

            this.trigger('error:save');
            this.trigger('cancel:save', {reason: 'error'});
        };

        return new Promise((resolve, reject) => {
            model
                .save(
                    setAttributes,
                    {
                        patch: !model.isNew(),
                        headers: headers,
                    },
                )
                .then(() => {
                    this.trigger('save', initialAttributes);

                    this.afterSave();

                    if (this.isNew) {
                        this.isNew = false;
                    }

                    this.trigger('after:save');
                    model.trigger('after:save');

                    resolve();
                })
                .catch(xhr => {
                    onError(xhr, reject, resolve);
                });
        });
    }

    
    handleSaveError(xhr, options, saveResolve, saveReject) {
        let handlerData = null;

        if (~[409, 500].indexOf(xhr.status)) {
            const statusReason = xhr.getResponseHeader('X-Status-Reason');

            if (!statusReason) {
                return Promise.resolve(false);
            }

            try {
                handlerData = JSON.parse(statusReason);
            }
            catch (e) {}

            if (!handlerData) {
                handlerData = {
                    reason: statusReason.toString(),
                };

                if (xhr.responseText) {
                    let data;

                    try {
                        data = JSON.parse(xhr.responseText);
                    }
                    catch (e) {
                        console.error('Could not parse error response body.');

                        return Promise.resolve(false);
                    }

                    handlerData.data = data;
                }
            }
        }

        if (!handlerData || !handlerData.reason) {
            return Promise.resolve(false);
        }

        const reason = handlerData.reason;

        const handlerName =
            this.getMetadata()
                .get(['clientDefs', this.scope, 'saveErrorHandlers', reason]) ||
            this.getMetadata()
                .get(['clientDefs', 'Global', 'saveErrorHandlers', reason]);

        return new Promise(resolve => {
            if (handlerName) {
                Espo.loader.require(handlerName, Handler => {
                    const handler = new Handler(this);

                    handler.process(handlerData.data, options);

                    resolve(false);
                });

                xhr.errorIsHandled = true;

                return;
            }

            const methodName = 'errorHandler' + Espo.Utils.upperCaseFirst(reason);

            if (methodName in this) {
                xhr.errorIsHandled = true;

                const skipReject = this[methodName](handlerData.data, options, saveResolve, saveReject);

                resolve(skipReject || false);

                return;
            }

            resolve(false);
        });
    }

    
    fetch() {
        let data = {};
        const fieldViews = this.getFieldViews();

        for (const i in fieldViews) {
            const view = fieldViews[i];

            if (!view.isEditMode()) {
                continue;
            }

            if (!view.disabled && !view.readOnly && view.isFullyRendered()) {
                data = {...data, ...view.fetch()};
            }
        }

        return data;
    }

    
    processFetch() {
        const data = this.fetch();

        this.model.set(data);

        if (this.validate()) {
            return null;
        }

        return data;
    }

    
    populateDefaults() {
        const populator = new DefaultsPopulator(
            this.getUser(),
            this.getPreferences(),
            this.getAcl(),
            this.getConfig()
        );

        populator.populate(this.model);
    }

    
    
    errorHandlerDuplicate(duplicates) {}

    
    _handleDependencyAttributes() {
        
        Object.keys(this.dependencyDefs || {}).forEach(attr => {
            this._handleDependencyAttribute(attr);
        });
    }

    
    _handleDependencyAttribute(attr) {
        
        const data = this.dependencyDefs[attr];
        const value = this.model.get(attr);

        if (value in (data.map || {})) {
            (data.map[value] || []).forEach((item) => {
                this._doDependencyAction(item);
            });

            return;
        }

        if ('default' in data) {
            (data.default || []).forEach((item) => {
                this._doDependencyAction(item);
            });
        }
    }

    
    _doDependencyAction(data) {
        const action = data.action;

        const methodName = 'dependencyAction' + Espo.Utils.upperCaseFirst(action);

        if (methodName in this && typeof this.methodName === 'function') {
            this.methodName(data);

            return;
        }

        const fieldList = data.fieldList || data.fields || [];
        const panelList = data.panelList || data.panels || [];

        switch (action) {
            case 'hide':
                panelList.forEach((item) => {
                    this.hidePanel(item);
                });

                fieldList.forEach((item) => {
                    this.hideField(item);
                });

                break;

            case 'show':
                panelList.forEach((item) => {
                    this.showPanel(item);
                });

                fieldList.forEach((item) => {
                    this.showField(item);
                });

                break;

            case 'setRequired':
                fieldList.forEach((field) => {
                    this.setFieldRequired(field);
                });

                break;

            case 'setNotRequired':
                fieldList.forEach((field) => {
                    this.setFieldNotRequired(field);
                });

                break;

            case 'setReadOnly':
                fieldList.forEach((field) => {
                    this.setFieldReadOnly(field);
                });

                break;

            case 'setNotReadOnly':
                fieldList.forEach((field) => {
                    this.setFieldNotReadOnly(field);
                });

                break;
        }
    }

    
    createField(name, view, params, mode, readOnly, options) {
        const o = {
            model: this.model,
            mode: mode || 'edit',
            selector: '.field[data-name="' + name + '"]',
            defs: {
                name: name,
                params: params || {},
            },
        };

        if (readOnly) {
            o.readOnly = true;
        }

        view = view || this.model.getFieldParam(name, 'view');

        if (!view) {
            const type = this.model.getFieldType(name) || 'base';
            view = this.getFieldManager().getViewName(type);
        }

        if (options) {
            for (const param in options) {
                o[param] = options[param];
            }
        }

        if (this.recordHelper.getFieldStateParam(name, 'hidden')) {
            o.disabled = true;
        }

        if (this.recordHelper.getFieldStateParam(name, 'readOnly')) {
            o.readOnly = true;
        }

        if (this.recordHelper.getFieldStateParam(name, 'required') !== null) {
            o.defs.params.required = this.recordHelper.getFieldStateParam(name, 'required');
        }

        if (this.recordHelper.hasFieldOptionList(name)) {
            o.customOptionList = this.recordHelper.getFieldOptionList(name);
        }

        const viewKey = name + 'Field';

        this.createView(viewKey, view, o);

        if (!~this.fieldList.indexOf(name)) {
            this.fieldList.push(name);
        }
    }

    
    getFocusedFieldView() {
        const $active = $(window.document.activeElement);

        if (!$active.length) {
            return null;
        }

        const $field = $active.closest('.field');

        if (!$field.length) {
            return null;
        }

        const name = $field.attr('data-name');

        if (!name) {
            return null;
        }

        return this.getFieldView(name);
    }

    
    exit(after) {}
}

export default BaseRecordView;
