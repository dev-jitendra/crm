



import View from 'view';


class PanelsContainerRecordView extends View {

    
    panelSoftLockedTypeList = ['default', 'acl', 'delimiter', 'dynamicLogic']

    

    

    

    
    panelList = null

    
    hasTabs = false

    
    tabDataList = null

    
    currentTab = 0

    
    scope = ''

    
    entityType =  ''

    
    name =  ''

    
    mode = 'detail'

    data() {
        const tabDataList = this.hasTabs ? this.getTabDataList() : [];

        return {
            panelList: this.panelList,
            scope: this.scope,
            entityType: this.entityType,
            tabDataList: tabDataList,
        };
    }

    events = {
        'click .action': function (e) {
            const $target = $(e.currentTarget);
            const panel = $target.data('panel');

            if (!panel) {
                return;
            }

            const panelView = this.getView(panel);

            if (!panelView) {
                return;
            }

            let actionItems;

            if (
                typeof panelView.getButtonList === 'function' &&
                typeof panelView.getActionList === 'function'
            ) {
                actionItems = [...panelView.getButtonList(), ...panelView.getActionList()];
            }

            Espo.Utils.handleAction(panelView, e.originalEvent, e.currentTarget, {
                actionItems: actionItems,
                className: 'panel-action',
            });

            

            
        },
        'click .panels-show-more-delimiter [data-action="showMorePanels"]': 'actionShowMorePanels',
        
        'click .tabs > button': function (e) {
            const tab = parseInt($(e.currentTarget).attr('data-tab'));

            this.selectTab(tab);
        },
    }

    afterRender() {
        this.adjustPanels();
    }

    adjustPanels() {
        if (!this.isRendered()) {
            return;
        }

        const $panels = this.$el.find('> .panel');

        $panels
            .removeClass('first')
            .removeClass('last')
            .removeClass('in-middle');

        const $visiblePanels = $panels.filter(`:not(.tab-hidden):not(.hidden)`);

        const groups = [];
        let currentGroup = [];
        let inTab = false;

        $visiblePanels.each((i, el) => {
            const $el = $(el);

            let breakGroup = false;

            if (
                !breakGroup &&
                this.hasTabs &&
                !inTab &&
                $el.attr('data-tab') !== '-1'
            ) {
                inTab = true;
                breakGroup = true;
            }

            if (!breakGroup && !$el.hasClass('sticked')) {
                breakGroup = true;
            }

            if (breakGroup) {
                if (i !== 0) {
                    groups.push(currentGroup);
                }

                currentGroup = [];
            }

            currentGroup.push($el);

            if (i === $visiblePanels.length - 1) {
                groups.push(currentGroup);
            }
        });

        groups.forEach(group => {
            group.forEach(($el, i) => {
                if (i === group.length - 1) {
                    if (i === 0) {
                        return;
                    }

                    $el.addClass('last')

                    return;
                }

                if (i === 0 && group.length) {
                    $el.addClass('first')

                    return;
                }

                $el.addClass('in-middle');
            });
        });
    }

    
    setReadOnly() {
        this.readOnly = true;
    }

    
    setNotReadOnly(onlyNotSetAsReadOnly) {
        this.readOnly = false;

        if (onlyNotSetAsReadOnly) {
            this.panelList.forEach(item => {
                this.applyAccessToActions(item.buttonList);
                this.applyAccessToActions(item.actionList);

                if (this.isRendered()) {
                    const actionsView = this.getView(item.actionsViewKey);

                    if (actionsView) {
                        actionsView.reRender();
                    }
                }
            });
        }
    }

    
    applyAccessToActions(actionList) {
        if (!actionList) {
            return;
        }

        actionList.forEach(item => {
            if (!Espo.Utils.checkActionAvailability(this.getHelper(), item)) {
                item.hidden = true;

                return;
            }

            if (Espo.Utils.checkActionAccess(this.getAcl(), this.model, item, true)) {
                if (item.isHiddenByAcl) {
                    item.isHiddenByAcl = false;
                    item.hidden = false;
                }
            }
            else {
                if (!item.hidden) {
                    item.isHiddenByAcl = true;
                    item.hidden = true;
                }
            }
        });
    }

    
    setupPanelViews() {
        this.panelList.forEach(p => {
            const name = p.name;

            let options = {
                model: this.model,
                panelName: name,
                selector: '.panel[data-name="' + name + '"] > .panel-body',
                defs: p,
                mode: this.mode,
                recordHelper: this.recordHelper,
                inlineEditDisabled: this.inlineEditDisabled,
                readOnly: this.readOnly,
                disabled: p.hidden || false,
                recordViewObject: this.recordViewObject,
                dataObject: this.options.dataObject,
            };

            options = _.extend(options, p.options);

            this.createView(name, p.view, options, (view) => {
                if ('getActionList' in view) {
                    p.actionList = view.getActionList();

                    this.applyAccessToActions(p.actionList);
                }

                if ('getButtonList' in view) {
                    p.buttonList = view.getButtonList();
                    this.applyAccessToActions(p.buttonList);
                }

                if (view.titleHtml) {
                    p.titleHtml = view.titleHtml;
                }
                else {
                    if (p.label) {
                        p.title = this.translate(p.label, 'labels', this.scope);
                    }
                    else {
                        p.title = view.title;
                    }
                }

                this.createView(name + 'Actions', 'views/record/panel-actions', {
                    selector: '.panel[data-name="' + p.name + '"] > .panel-heading > .panel-actions-container',
                    model: this.model,
                    defs: p,
                    scope: this.scope,
                    entityType: this.entityType,
                });
            });
        });
    }

    
    setupPanels() {}

    
    getFieldViews(withHidden) {
        let fields = {};

        this.panelList.forEach(p => {
            const panelView = this.getView(p.name);

            if ((!panelView.disabled || withHidden) && 'getFieldViews' in panelView) {
                fields = _.extend(fields, panelView.getFieldViews());
            }
        });

        return fields;
    }

    
    
    getFields() {
        return this.getFieldViews();
    }

    
    fetch() {
        let data = {};

        this.panelList.forEach(p => {
            const panelView = this.getView(p.name);

            if (!panelView.disabled && 'fetch' in panelView) {
                data = _.extend(data, panelView.fetch());
            }
        });

        return data;
    }

    
    hasPanel(name) {
        return !!this.panelList.find(item => item.name === name);
    }

    processShowPanel(name, callback, wasShown) {
        if (this.recordHelper.getPanelStateParam(name, 'hidden')) {
            return;
        }

        if (!this.hasPanel(name)) {
            return;
        }

        this.panelList.filter(item => item.name === name).forEach(item => {
            item.hidden = false;

            if (typeof item.tabNumber !== 'undefined') {
                this.controlTabVisibilityShow(item.tabNumber);
            }
        });

        this.showPanelFinalize(name, callback, wasShown);
    }

    processHidePanel(name, callback) {
        if (!this.recordHelper.getPanelStateParam(name, 'hidden')) {
            return;
        }

        if (!this.hasPanel(name)) {
            return;
        }

         this.panelList.filter(item => item.name === name).forEach(item => {
            item.hidden = true;

            if (typeof item.tabNumber !== 'undefined') {
                this.controlTabVisibilityHide(item.tabNumber);
            }
        });

        this.hidePanelFinalize(name, callback);
    }

    showPanelFinalize(name, callback, wasShown) {
        const process = (wasRendered) => {
            const view = this.getView(name);

            if (view) {
                view.$el.closest('.panel').removeClass('hidden');

                view.disabled = false;
                view.trigger('show');
                view.trigger('panel-show-propagated');

                if (wasRendered && !wasShown && view.getFieldViews) {
                    const fields = view.getFieldViews();

                    if (fields) {
                        for (const i in fields) {
                            fields[i].reRender();
                        }
                    }
                }
            }

            if (typeof callback === 'function') {
                callback.call(this);
            }
        };

        if (this.isRendered()) {
            process(true);

            this.adjustPanels();

            return;
        }

        this.once('after:render', () => {
            process();
        });
    }

    hidePanelFinalize(name, callback) {
        if (this.isRendered()) {
            const view = this.getView(name);

            if (view) {
                view.$el.closest('.panel').addClass('hidden');
                view.disabled = true;
                view.trigger('hide');
            }

            if (typeof callback === 'function') {
                callback.call(this);
            }

            this.adjustPanels();

            return;
        }

        if (typeof callback === 'function') {
            this.once('after:render', () => {
                callback.call(this);
            });
        }
    }

    showPanel(name, softLockedType, callback) {
        if (!this.hasPanel(name)) {
            return;
        }

        if (this.recordHelper.getPanelStateParam(name, 'hiddenLocked')) {
            return;
        }

        if (softLockedType) {
            const param = 'hidden' + Espo.Utils.upperCaseFirst(softLockedType) + 'Locked';

            this.recordHelper.setPanelStateParam(name, param, false);

            for (let i = 0; i < this.panelSoftLockedTypeList.length; i++) {
                const iType = this.panelSoftLockedTypeList[i];

                if (iType === softLockedType) {
                    continue;
                }

                const iParam = 'hidden' + Espo.Utils.upperCaseFirst(iType) + 'Locked';

                if (this.recordHelper.getPanelStateParam(name, iParam)) {
                    return;
                }
            }
        }

        const wasShown = this.recordHelper.getPanelStateParam(name, 'hidden') === false;

        this.recordHelper.setPanelStateParam(name, 'hidden', false);

        this.processShowPanel(name, callback, wasShown);
    }

    hidePanel(name, locked, softLockedType, callback) {
        if (!this.hasPanel(name)) {
            return;
        }

        this.recordHelper.setPanelStateParam(name, 'hidden', true);

        if (locked) {
            this.recordHelper.setPanelStateParam(name, 'hiddenLocked', true);
        }

        if (softLockedType) {
            const param = 'hidden' + Espo.Utils.upperCaseFirst(softLockedType) + 'Locked';

            this.recordHelper.setPanelStateParam(name, param, true);
        }

        this.processHidePanel(name, callback);
    }

    alterPanels(layoutData) {
        layoutData = layoutData || this.layoutData || {};

        const tabBreakIndexList = [];

        const tabDataList = [];

        for (const name in layoutData) {
            const item = layoutData[name];

            if (name === '_delimiter_') {
                this.panelList.push({
                    name: name,
                });
            }

            if (item.tabBreak) {
                tabBreakIndexList.push(item.index);

                tabDataList.push({
                    index: item.index,
                    label: item.tabLabel,
                })
            }
        }

        
        this.tabDataList = tabDataList.sort((v1, v2) => v1.index - v2.index);

        this.panelList = this.panelList.filter(item => {
            return !this.recordHelper.getPanelStateParam(item.name, 'hiddenLocked');
        });

        const newList = [];

        this.panelList.forEach((item, i) => {
            item.index = ('index' in item) ? item.index : i;

            let allowedInLayout = false;

            if (item.name) {
                const itemData = layoutData[item.name] || {};

                if (itemData.disabled) {
                    return;
                }

                if (layoutData[item.name]) {
                    allowedInLayout = true;
                }

                for (const i in itemData) {
                    item[i] = itemData[i];
                }
            }

            if (item.disabled && !allowedInLayout) {
                return;
            }

            item.tabNumber = tabBreakIndexList.length -
                tabBreakIndexList.slice().reverse().findIndex(index => item.index > index) - 1;

            if (item.tabNumber === tabBreakIndexList.length) {
                item.tabNumber = -1;
            }

            newList.push(item);
        });

        newList.sort((v1, v2) => v1.index - v2.index);

        const firstTabIndex = newList.findIndex(item => item.tabNumber !== -1);

        if (firstTabIndex !== -1) {
            newList[firstTabIndex].isTabsBeginning = true;
            this.hasTabs = true;
            this.currentTab = newList[firstTabIndex].tabNumber;

            this.panelList
                .filter(item => item.tabNumber !== -1 && item.tabNumber !== this.currentTab)
                .forEach(item => {
                    item.tabHidden = true;
                });

            this.panelList
                .forEach((item, i) => {
                    if (
                        item.tabNumber !== -1 &&
                        (i === 0 || this.panelList[i - 1].tabNumber !== item.tabNumber)
                    ) {
                        item.sticked = false;
                    }
                });
        }

        this.panelList = newList;

        if (this.recordViewObject && this.recordViewObject.dynamicLogic) {
            const dynamicLogic = this.recordViewObject.dynamicLogic;

            this.panelList.forEach(item => {
                if (item.dynamicLogicVisible) {
                    dynamicLogic.addPanelVisibleCondition(item.name, item.dynamicLogicVisible);

                    if (this.recordHelper.getPanelStateParam(item.name, 'hidden')) {
                        item.hidden = true;
                    }
                }

                if (item.style && item.style !== 'default' && item.dynamicLogicStyled) {
                    dynamicLogic.addPanelStyledCondition(item.name, item.dynamicLogicStyled);
                }
            });
        }

        if (
            this.hasTabs &&
            this.options.isReturn &&
            this.isStoredTabForThisRecord()
        ) {
            this.selectStoredTab();
        }
    }

    setupPanelsFinal() {
        let afterDelimiter = false;
        let rightAfterDelimiter = false;

        let index = -1;

        this.panelList.forEach((p, i) => {
            if (p.name === '_delimiter_') {
                afterDelimiter = true;
                rightAfterDelimiter = true;
                index = i;

                return;
            }

            if (afterDelimiter) {
                p.hidden = true;
                p.hiddenAfterDelimiter = true;

                this.recordHelper.setPanelStateParam(p.name, 'hidden', true);
                this.recordHelper.setPanelStateParam(p.name, 'hiddenDelimiterLocked', true);
            }

            if (rightAfterDelimiter) {
                p.isRightAfterDelimiter = true;
                rightAfterDelimiter = false;
            }
        });

        if (~index) {
            this.panelList.splice(index, 1);
        }

        this.panelsAreSet = true;

        this.trigger('panels-set');
    }

    actionShowMorePanels() {
        this.panelList.forEach(p => {
            if (!p.hiddenAfterDelimiter) {
                return;
            }

            delete p.isRightAfterDelimiter;

            this.showPanel(p.name, 'delimiter');
        });

        this.$el.find('.panels-show-more-delimiter').remove();
    }

    onPanelsReady(callback) {
        Promise.race([
            new Promise(resolve => {
                if (this.panelsAreSet) {
                    resolve();
                }
            }),
            new Promise(resolve => {
                this.once('panels-set', resolve);
            })
        ]).then(() => {
            callback.call(this);
        });
    }

    getTabDataList() {
        return this.tabDataList.map((item, i) => {
            let label = item.label;

            if (!label) {
                label = (i + 1).toString();
            }
            else if (label[0] === '$') {
                label = this.translate(label.substring(1), 'tabs', this.scope);
            }

            const hidden = this.panelList
                .filter(panel => panel.tabNumber === i)
                .findIndex(panel => !this.recordHelper.getPanelStateParam(panel.name, 'hidden')) === -1;

            return {
                label: label,
                isActive: i === this.currentTab,
                hidden: hidden,
            };
        });
    }

    selectTab(tab) {
        this.currentTab = tab;

        if (this.isRendered()) {
            $('body > .popover').remove();

            this.$el.find('.tabs > button').removeClass('active');
            this.$el.find(`.tabs > button[data-tab="${tab}"]`).addClass('active');

            this.$el.find('.panel[data-tab]:not([data-tab="-1"])').addClass('tab-hidden');
            this.$el.find(`.panel[data-tab="${tab}"]`).removeClass('tab-hidden');
        }

        this.adjustPanels();

        this.panelList
            .filter(item => item.tabNumber === tab && item.name)
            .forEach(item => {
                const view = this.getView(item.name);

                if (view) {
                    view.trigger('tab-show');

                    view.propagateEvent('panel-show-propagated');
                }

                item.tabHidden = false;
            });

        this.panelList
            .filter(item => item.tabNumber !== tab && item.name)
            .forEach(item => {
                const view = this.getView(item.name);

                if (view) {
                    view.trigger('tab-hide');
                }

                if (item.tabNumber > -1) {
                    item.tabHidden = true;
                }
            });

        this.storeTab();
    }

    
    storeTab() {
        const key = 'tab_' + this.name;
        const keyRecord = 'tab_' + this.name + '_record';

        this.getSessionStorage().set(key, this.currentTab);
        this.getSessionStorage().set(keyRecord, this.entityType + '_' + this.model.id);
    }

    
    isStoredTabForThisRecord() {
        const keyRecord = 'tab_' + this.name + '_record';

        return this.getSessionStorage().get(keyRecord) === this.entityType + '_' + this.model.id;
    }

    
    selectStoredTab() {
        const key = 'tab_' + this.name;

        const tab = this.getSessionStorage().get(key);

        if (tab > 0) {
            this.selectTab(tab);
        }
    }

    
    controlTabVisibilityShow(tab) {
        if (!this.hasTabs) {
            return;
        }

        if (this.isBeingRendered()) {
            this.once('after:render', () => this.controlTabVisibilityShow(tab));

            return;
        }

        this.$el.find(`.tabs > [data-tab="${tab.toString()}"]`).removeClass('hidden');
    }

    
    controlTabVisibilityHide(tab) {
        if (!this.hasTabs) {
            return;
        }

        if (this.isBeingRendered()) {
            this.once('after:render', () => this.controlTabVisibilityHide(tab));

            return;
        }

        const panelList = this.panelList.filter(panel => panel.tabNumber === tab);

        const allIsHidden = panelList
            .findIndex(panel => !this.recordHelper.getPanelStateParam(panel.name, 'hidden')) === -1;

        if (!allIsHidden) {
            return;
        }

        const $tab = this.$el.find(`.tabs > [data-tab="${tab.toString()}"]`);

        $tab.addClass('hidden');

        if (this.currentTab === tab) {
            const firstVisiblePanel = this.panelList
                .find(panel => panel.tabNumber > -1 && !panel.hidden);

            const firstVisibleTab = firstVisiblePanel ?
                firstVisiblePanel.tabNumber : 0;

            this.selectTab(firstVisibleTab);
        }
    }
}

export default PanelsContainerRecordView;
