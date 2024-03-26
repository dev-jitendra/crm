



import MainView from 'views/main';
import SearchManager from 'search-manager';


class ListView extends MainView {

    
    template = 'list'

    
    name = 'List'

    
    optionsToPass = []

    
    headerView = 'views/header'

    
    searchView = 'views/record/search'

    
    recordView = 'views/record/list'

    
    recordKanbanView = 'views/record/kanban'

    
    searchPanel = true

    
    searchManager = null

    
    createButton = true

    
    quickCreate = false

    
    storeViewAfterCreate = false

    
    storeViewAfterUpdate = true

    
    keepCurrentRootUrl = false

    
    viewMode = ''

    
    viewModeList = null

    
    defaultViewMode = 'list'

    
    MODE_LIST = 'list'
    
    MODE_KANBAN = 'kanban'

    
    shortcutKeys = {
        
        'Control+Space': function (e) {
            this.handleShortcutKeyCtrlSpace(e);
        },
        
        'Control+Slash': function (e) {
            this.handleShortcutKeyCtrlSlash(e);
        },
        
        'Control+Comma': function (e) {
            this.handleShortcutKeyCtrlComma(e);
        },
        
        'Control+Period': function (e) {
            this.handleShortcutKeyCtrlPeriod(e);
        },
    }

    
    setup() {
        this.collection.maxSize = this.getConfig().get('recordsPerPage') || this.collection.maxSize;

        this.collectionUrl = this.collection.url;
        this.collectionMaxSize = this.collection.maxSize;

        this.setupModes();
        this.setViewMode(this.viewMode);

        if (this.getMetadata().get(['clientDefs', this.scope, 'searchPanelDisabled'])) {
            this.searchPanel = false;
        }

        if (this.getUser().isPortal()) {
            if (this.getMetadata().get(['clientDefs', this.scope, 'searchPanelInPortalDisabled'])) {
                this.searchPanel = false;
            }
        }

        if (this.getMetadata().get(['clientDefs', this.scope, 'createDisabled'])) {
            this.createButton = false;
        }

        this.entityType = this.collection.entityType;

        this.headerView = this.options.headerView || this.headerView;
        this.recordView = this.options.recordView || this.recordView;
        this.searchView = this.options.searchView || this.searchView;

        this.setupHeader();

        this.defaultOrderBy = this.defaultOrderBy || this.collection.orderBy;
        this.defaultOrder = this.defaultOrder || this.collection.order;

        this.collection.setOrder(this.defaultOrderBy, this.defaultOrder, true);

        if (this.searchPanel) {
            this.setupSearchManager();
        }

        this.setupSorting();

        if (this.searchPanel) {
            this.setupSearchPanel();
        }

        if (this.createButton) {
            this.setupCreateButton();
        }

        if (this.options.params && this.options.params.fromAdmin) {
            this.keepCurrentRootUrl = true;
        }
    }

    setupFinal() {
        super.setupFinal();

        this.wait(
            this.getHelper().processSetupHandlers(this, 'list')
        );
    }

    
    setupModes() {
        this.defaultViewMode = this.options.defaultViewMode ||
            this.getMetadata().get(['clientDefs', this.scope, 'listDefaultViewMode']) ||
            this.defaultViewMode;

        this.viewMode = this.viewMode || this.defaultViewMode;

        const viewModeList = this.options.viewModeList ||
            this.viewModeList ||
            this.getMetadata().get(['clientDefs', this.scope, 'listViewModeList']);

        if (viewModeList) {
            this.viewModeList = viewModeList;
        }
        else {
            this.viewModeList = [this.MODE_LIST];

            if (this.getMetadata().get(['clientDefs', this.scope, 'kanbanViewMode'])) {
                if (!~this.viewModeList.indexOf(this.MODE_KANBAN)) {
                    this.viewModeList.push(this.MODE_KANBAN);
                }
            }
        }

        if (this.viewModeList.length > 1) {
            let viewMode = null;

            const modeKey = 'listViewMode' + this.scope;

            if (this.getStorage().has('state', modeKey)) {
                const storedViewMode = this.getStorage().get('state', modeKey);

                if (storedViewMode && this.viewModeList.includes(storedViewMode)) {
                    viewMode = storedViewMode;
                }
            }

            if (!viewMode) {
                viewMode = this.defaultViewMode;
            }

            this.viewMode = viewMode;
        }
    }

    
    setupHeader() {
        this.createView('header', this.headerView, {
            collection: this.collection,
            fullSelector: '#main > .page-header',
            scope: this.scope,
            isXsSingleRow: true,
        });
    }

    
    setupCreateButton() {
        if (this.quickCreate) {
            this.menu.buttons.unshift({
                action: 'quickCreate',
                iconHtml: '<span class="fas fa-plus fa-sm"></span>',
                text: this.translate('Create ' +  this.scope, 'labels', this.scope),
                style: 'default',
                acl: 'create',
                aclScope: this.entityType || this.scope,
                title: 'Ctrl+Space',
            });

            return;
        }

        this.menu.buttons.unshift({
            link: '#' + this.scope + '/create',
            action: 'create',
            iconHtml: '<span class="fas fa-plus fa-sm"></span>',
            text: this.translate('Create ' +  this.scope,  'labels', this.scope),
            style: 'default',
            acl: 'create',
            aclScope: this.entityType || this.scope,
            title: 'Ctrl+Space',
        });
    }

    
    setupSearchPanel() {
        this.createSearchView();
    }

    
    createSearchView() {
        return this.createView('search', this.searchView, {
            collection: this.collection,
            fullSelector: '#main > .search-container',
            searchManager: this.searchManager,
            scope: this.scope,
            viewMode: this.viewMode,
            viewModeList: this.viewModeList,
            isWide: true,
        }, view => {
            this.listenTo(view, 'reset', () => this.resetSorting());

            if (this.viewModeList.length > 1) {
                this.listenTo(view, 'change-view-mode', mode => this.switchViewMode(mode));
            }
        });
    }

    
    switchViewMode(mode) {
        this.clearView('list');
        this.collection.isFetched = false;
        this.collection.reset();
        this.applyStoredSorting();
        this.setViewMode(mode, true);
        this.loadList();
    }

    
    setViewMode(mode, toStore) {
        this.viewMode = mode;

        this.collection.url = this.collectionUrl;
        this.collection.maxSize = this.collectionMaxSize;

        if (toStore) {
            const modeKey = 'listViewMode' + this.scope;

            this.getStorage().set('state', modeKey, mode);
        }

        if (this.searchView && this.getView('search')) {
            this.getSearchView().setViewMode(mode);
        }

        if (this.viewMode === this.MODE_KANBAN) {
            this.setViewModeKanban();

            return;
        }

        const methodName = 'setViewMode' + Espo.Utils.upperCaseFirst(this.viewMode);

        if (this[methodName]) {
            this[methodName]();
        }
    }

    
    setViewModeKanban() {
        this.collection.url = 'Kanban/' + this.scope;
        this.collection.maxSize = this.getConfig().get('recordsPerPageKanban');
        this.collection.resetOrderToDefault();
    }

    
    resetSorting() {
        this.getStorage().clear('listSorting', this.collection.entityType);
    }

    
    getSearchDefaultData() {
        return this.getMetadata().get('clientDefs.' + this.scope + '.defaultFilterData');
    }

    
    setupSearchManager() {
        const collection = this.collection;

        const searchManager = new SearchManager(
            collection,
            'list',
            this.getStorage(),
            this.getDateTime(),
            this.getSearchDefaultData()
        );

        searchManager.scope = this.scope;
        searchManager.loadStored();

        collection.where = searchManager.getWhere();

        this.searchManager = searchManager;
    }

    
    setupSorting() {
        if (!this.searchPanel) {
            return;
        }

        this.applyStoredSorting();
    }

    
    applyStoredSorting() {
        const sortingParams = this.getStorage().get('listSorting', this.collection.entityType) || {};

        if ('orderBy' in sortingParams) {
            this.collection.orderBy = sortingParams.orderBy;
        }

        if ('order' in sortingParams) {
            this.collection.order = sortingParams.order;
        }
    }

    
    getSearchView() {
        return this.getView('search');
    }

    
    getRecordView() {
        return this.getView('list');
    }

    
    getRecordViewName() {
        let viewName = this.getMetadata().get(['clientDefs', this.scope, 'recordViews', this.viewMode]);

        if (viewName) {
            return viewName;
        }

        if (this.viewMode === this.MODE_LIST) {
            return this.recordView;
        }

        if (this.viewMode === this.MODE_KANBAN) {
            return this.recordKanbanView;
        }

        const propertyName = 'record' + Espo.Utils.upperCaseFirst(this.viewMode) + 'View';

        viewName = this[propertyName];

        if (!viewName) {
            throw new Error("No record view.");
        }

        return viewName;
    }

    
    cancelRender() {
        if (this.hasView('list')) {
            this.getRecordView();

            if (this.getRecordView().isBeingRendered()) {
                this.getRecordView().cancelRender();
            }
        }

        super.cancelRender();
    }

    
    afterRender() {
        Espo.Ui.notify(false);

        if (!this.hasView('list')) {
            this.loadList();
        }

        
        this.$el.get(0).focus({preventScroll: true});
    }

    
    loadList() {
        if ('isFetched' in this.collection && this.collection.isFetched) {
            this.createListRecordView(false);

            return;
        }

        Espo.Ui.notify(' ... ');

        this.createListRecordView(true);
    }

    
    prepareRecordViewOptions(options) {}

    
    createListRecordView(fetch) {
        const o = {
            collection: this.collection,
            selector: '.list-container',
            scope: this.scope,
            skipBuildRows: true,
            shortcutKeysEnabled: true,
            forceDisplayTopBar: true,
            additionalRowActionList: this.getMetadata().get(`clientDefs.${this.scope}.rowActionList`),
            settingsEnabled: true,
        };

        if (this.getHelper().isXsScreen()) {
            o.type = 'listSmall';
        }

        this.optionsToPass.forEach(option => {
            o[option] = this.options[option];
        });

        if (this.keepCurrentRootUrl) {
            o.keepCurrentRootUrl = true;
        }

        if (
            this.getConfig().get('listPagination') ||
            this.getMetadata().get(['clientDefs', this.scope, 'listPagination'])
        ) {
            
            console.warn(`'listPagination' parameter is deprecated and will be removed in the future.`);

            o.pagination = true;
        }

        this.prepareRecordViewOptions(o);

        const listViewName = this.getRecordViewName();

        return this.createView('list', listViewName, o, view => {
            if (!this.hasParentView()) {
                view.undelegateEvents();

                return;
            }

            this.listenToOnce(view, 'after:render', () => {
                if (!this.hasParentView()) {
                    view.undelegateEvents();

                    this.clearView('list');
                }
            });

            if (!fetch) {
                Espo.Ui.notify(false);
            }

            if (this.searchPanel) {
                this.listenTo(view, 'sort', obj => {
                    this.getStorage().set('listSorting', this.collection.entityType, obj);
                });
            }

            if (!fetch) {
                view.render();

                return;
            }

            view.getSelectAttributeList(selectAttributeList => {
                if (this.options.mediator && this.options.mediator.abort) {
                    return;
                }

                if (selectAttributeList) {
                    this.collection.data.select = selectAttributeList.join(',');
                }

                Espo.Ui.notify(' ... ');

                this.collection.fetch({main: true})
                    .then(() => Espo.Ui.notify(false));
            });
        });
    }

    
    getHeader() {
        const $root = $('<span>')
            .text(this.getLanguage().translate(this.scope, 'scopeNamesPlural'));

        if (this.options.params && this.options.params.fromAdmin) {
            const $root = $('<a>')
                .attr('href', '#Admin')
                .text(this.translate('Administration', 'labels', 'Admin'));

            const $scope = $('<span>')
                .text(this.getLanguage().translate(this.scope, 'scopeNamesPlural'));

            return this.buildHeaderHtml([$root, $scope]);
        }

        const headerIconHtml = this.getHeaderIconHtml();

        if (headerIconHtml) {
            $root.prepend(headerIconHtml);
        }

        return this.buildHeaderHtml([$root]);
    }

    
    updatePageTitle() {
        this.setPageTitle(this.getLanguage().translate(this.scope, 'scopeNamesPlural'));
    }

    
    getCreateAttributes() {}

    
    prepareCreateReturnDispatchParams(params) {}

    
    actionQuickCreate(data) {
        data = data || {};

        const attributes = this.getCreateAttributes() || {};

        Espo.Ui.notify(' ... ');

        const viewName = this.getMetadata().get('clientDefs.' + this.scope + '.modalViews.edit') ||
            'views/modals/edit';

        let options = {
            scope: this.scope,
            attributes: attributes,
        };

        if (this.keepCurrentRootUrl) {
            options.rootUrl = this.getRouter().getCurrentUrl();
        }

        if (data.focusForCreate) {
            options.focusForCreate = true;
        }

        const returnDispatchParams = {
            controller: this.scope,
            action: null,
            options: {isReturn: true},
        };

        this.prepareCreateReturnDispatchParams(returnDispatchParams);

        options = {
            ...options,
            returnUrl: this.getRouter().getCurrentUrl(),
            returnDispatchParams: returnDispatchParams,
        };

        return this.createView('quickCreate', viewName, options, (view) => {
            view.render();
            view.notify(false);

            this.listenToOnce(view, 'after:save', () => {
                this.collection.fetch();
            });
        });
    }

    
    actionCreate(data) {
        data = data || {};

        const router = this.getRouter();

        const url = '#' + this.scope + '/create';
        const attributes = this.getCreateAttributes() || {};

        let options = {attributes: attributes};

        if (this.keepCurrentRootUrl) {
            options.rootUrl = this.getRouter().getCurrentUrl();
        }

        if (data.focusForCreate) {
            options.focusForCreate = true;
        }

        const returnDispatchParams = {
            controller: this.scope,
            action: null,
            options: {isReturn: true},
        };

        this.prepareCreateReturnDispatchParams(returnDispatchParams);

        options = {
            ...options,
            returnUrl: this.getRouter().getCurrentUrl(),
            returnDispatchParams: returnDispatchParams,
        };

        router.navigate(url, {trigger: false});
        router.dispatch(this.scope, 'create', options);
    }

    
    isActualForReuse() {
        return 'isFetched' in this.collection && this.collection.isFetched;
    }

    
    handleShortcutKeyCtrlSpace(e) {
        if (!this.createButton) {
            return;
        }

        

        if (!this.getAcl().checkScope(this.scope, 'create')) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (this.quickCreate) {
            this.actionQuickCreate({focusForCreate: true});

            return;
        }

        this.actionCreate({focusForCreate: true});
    }

    
    handleShortcutKeyCtrlSlash(e) {
        if (!this.searchPanel) {
            return;
        }

        const $search = this.$el.find('input.text-filter').first();

        if (!$search.length) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        $search.focus();
    }

    
    
    handleShortcutKeyCtrlComma(e) {
        if (!this.getSearchView()) {
            return;
        }

        this.getSearchView().selectPreviousPreset();
    }

    
    
    handleShortcutKeyCtrlPeriod(e) {
        if (!this.getSearchView()) {
            return;
        }

        this.getSearchView().selectNextPreset();
    }
}

export default ListView;
