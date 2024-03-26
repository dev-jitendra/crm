



import MainView from 'views/main';
import SearchManager from 'search-manager';


class ListRelatedView extends MainView {

    
    template = 'list'

    
    name = 'ListRelated'

    
    headerView = 'views/header'

    
    searchView = 'views/record/search'

    
    recordView = 'views/record/list'

    
    searchPanel = true

    
    searchManager = null

    
    optionsToPass = []

    
    keepCurrentRootUrl = false

    
    viewMode = ''

    
    viewModeList = null

    
    defaultViewMode = 'list'

    
    MODE_LIST = 'list'

    
    rowActionsView = 'views/record/row-actions/relationship'

    
    createButton = true

    
    unlinkDisabled = false

    
    filtersDisabled = false

    
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
        this.link = this.options.link;

        if (!this.link) {
            console.error(`Link not passed.`);
            throw new Error();
        }

        if (!this.model) {
            console.error(`Model not passed.`);
            throw new Error();
        }

        if (!this.collection) {
            console.error(`Collection not passed.`);
            throw new Error();
        }

        this.panelDefs = this.getMetadata().get(['clientDefs', this.scope, 'relationshipPanels', this.link]) || {};

        if (this.panelDefs.fullFormDisabled) {
            console.error(`Full-form disabled.`);

            throw new Error();
        }

        this.collection.maxSize = this.getConfig().get('recordsPerPage') || this.collection.maxSize;
        this.collectionUrl = this.collection.url;
        this.collectionMaxSize = this.collection.maxSize;

        this.foreignScope = this.collection.entityType;

        this.setupModes();
        this.setViewMode(this.viewMode);

        if (this.getMetadata().get(['clientDefs', this.foreignScope, 'searchPanelDisabled'])) {
            this.searchPanel = false;
        }

        if (this.getUser().isPortal()) {
            if (this.getMetadata().get(['clientDefs', this.foreignScope, 'searchPanelInPortalDisabled'])) {
                this.searchPanel = false;
            }
        }

        if (this.getMetadata().get(['clientDefs', this.foreignScope, 'createDisabled'])) {
            this.createButton = false;
        }

        
        if (
            this.panelDefs.create === false ||
            this.panelDefs.createDisabled ||
            this.panelDefs.createAction
        ) {
            this.createButton = false;
        }

        this.entityType = this.collection.entityType;

        this.headerView = this.options.headerView || this.headerView;
        this.recordView = this.options.recordView || this.recordView;
        this.searchView = this.options.searchView || this.searchView;

        this.setupHeader();

        this.defaultOrderBy = this.panelDefs.orderBy || this.collection.orderBy;
        this.defaultOrder = this.panelDefs.orderDirection || this.collection.order;

        if (this.panelDefs.orderBy && !this.panelDefs.orderDirection) {
            this.defaultOrder = 'asc';
        }

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

        this.wait(
            this.getHelper().processSetupHandlers(this, 'list')
        );
    }

    
    setupModes() {
        this.defaultViewMode = this.options.defaultViewMode ||
            this.getMetadata().get(['clientDefs', this.foreignScope, 'listRelatedDefaultViewMode']) ||
            this.defaultViewMode;

        this.viewMode = this.viewMode || this.defaultViewMode;

        const viewModeList = this.options.viewModeList ||
            this.viewModeList ||
            this.getMetadata().get(['clientDefs', this.foreignScope, 'listRelatedViewModeList']);

        this.viewModeList = viewModeList ? viewModeList : [this.MODE_LIST];

        if (this.viewModeList.length > 1) {
            let viewMode = null;

            const modeKey = 'listRelatedViewMode' + this.scope + this.link;

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
        this.menu.buttons.unshift({
            action: 'quickCreate',
            iconHtml: '<span class="fas fa-plus fa-sm"></span>',
            text: this.translate('Create ' + this.foreignScope, 'labels', this.foreignScope),
            style: 'default',
            acl: 'create',
            aclScope: this.foreignScope,
            title: 'Ctrl+Space',
        });
    }

    
    setupSearchPanel() {
        this.createSearchView();
    }

    
    createSearchView() {
        let filterList = Espo.Utils
            .clone(this.getMetadata().get(['clientDefs', this.foreignScope, 'filterList']) || []);

        if (this.panelDefs.filterList) {
            this.panelDefs.filterList.forEach(item1 => {
                let isFound = false;
                const name1 = item1.name || item1;

                if (!name1 || name1 === 'all') {
                    return;
                }

                filterList.forEach(item2 => {
                    const name2 = item2.name || item2;

                    if (name1 === name2) {
                        isFound = true;
                    }
                });

                if (!isFound) {
                    filterList.push(item1);
                }
            });
        }

        if (this.filtersDisabled) {
            filterList = [];
        }

        return this.createView('search', this.searchView, {
            collection: this.collection,
            fullSelector: '#main > .search-container',
            searchManager: this.searchManager,
            scope: this.foreignScope,
            viewMode: this.viewMode,
            viewModeList: this.viewModeList,
            isWide: true,
            filterList: filterList,
        }, view => {
            if (this.viewModeList.length > 1) {
                this.listenTo(view, 'change-view-mode', mode => this.switchViewMode(mode));
            }
        });
    }

    
    switchViewMode(mode) {
        this.clearView('list');
        this.collection.isFetched = false;
        this.collection.reset();
        this.setViewMode(mode, true);
        this.loadList();
    }

    
    setViewMode(mode, toStore) {
        this.viewMode = mode;

        this.collection.url = this.collectionUrl;
        this.collection.maxSize = this.collectionMaxSize;

        if (toStore) {
            const modeKey = 'listViewMode' + this.scope + this.link;

            this.getStorage().set('state', modeKey, mode);
        }

        if (this.searchView && this.getView('search')) {
            this.getSearchView().setViewMode(mode);
        }

        const methodName = 'setViewMode' + Espo.Utils.upperCaseFirst(this.viewMode);

        if (this[methodName]) {
            this[methodName]();
        }
    }

    
    setupSearchManager() {
        const collection = this.collection;

        const searchManager = new SearchManager(
            collection,
            'list',
            null,
            this.getDateTime(),
            null
        );

        searchManager.scope = this.foreignScope;

        collection.where = searchManager.getWhere();

        this.searchManager = searchManager;
    }

    
    setupSorting() {}

    
    getSearchView() {
        return this.getView('search');
    }

    
    getRecordView() {
        return this.getView('list');
    }

    
    getRecordViewName() {
        if (this.viewMode === this.MODE_LIST) {
            return this.panelDefs.recordListView ||
                this.getMetadata().get(['clientDefs', this.foreignScope, 'recordViews', this.MODE_LIST]) ||
                    this.recordView;
        }

        const propertyName = 'record' + Espo.Utils.upperCaseFirst(this.viewMode) + 'View';

        return this.getMetadata().get(['clientDefs', this.foreignScope, 'recordViews', this.viewMode]) ||
            this[propertyName];
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

    
    createListRecordView() {
        let o = {
            collection: this.collection,
            selector: '.list-container',
            scope: this.foreignScope,
            skipBuildRows: true,
            shortcutKeysEnabled: true,
        };

        this.optionsToPass.forEach(option => {
            o[option] = this.options[option];
        });

        if (this.keepCurrentRootUrl) {
            o.keepCurrentRootUrl = true;
        }

        if (this.panelDefs.layout && typeof this.panelDefs.layout === 'string') {
            o.layoutName = this.panelDefs.layout;
        }

        o.rowActionsView = this.panelDefs.readOnly ? false :
            (this.panelDefs.rowActionsView || this.rowActionsView);

        if (
            this.getConfig().get('listPagination') ||
            this.getMetadata().get(['clientDefs', this.foreignScope, 'listPagination'])
        ) {
            o.pagination = true;
        }

        const massUnlinkDisabled = this.panelDefs.massUnlinkDisabled ||
            this.panelDefs.unlinkDisabled || this.unlinkDisabled;

        o = {
            unlinkMassAction: !massUnlinkDisabled,
            skipBuildRows: true,
            buttonsDisabled: true,
            forceDisplayTopBar: true,
            rowActionsOptions:  {
                unlinkDisabled: this.panelDefs.unlinkDisabled || this.unlinkDisabled,
                editDisabled: this.panelDefs.editDisabled,
                removeDisabled: this.panelDefs.removeDisabled,
            },
            additionalRowActionList: this.panelDefs.rowActionList,
            ...o,
            settingsEnabled: true,
        };

        if (this.getHelper().isXsScreen()) {
            o.type = 'listSmall';
        }

        this.prepareRecordViewOptions(o);

        const listViewName = this.getRecordViewName();

        this.createView('list', listViewName, o, view =>{
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

    
    actionQuickCreate() {
        const link = this.link;
        const foreignScope = this.foreignScope;
        const foreignLink = this.model.getLinkParam(link, 'foreign');

        let attributes = {};

        const attributeMap = this.getMetadata()
            .get(['clientDefs', this.scope, 'relationshipPanels', link, 'createAttributeMap']) || {};

        Object.keys(attributeMap)
            .forEach(attr => {
                attributes[attributeMap[attr]] = this.model.get(attr);
            });

        Espo.Ui.notify(' ... ');

        const handler = this.getMetadata()
            .get(['clientDefs', this.scope, 'relationshipPanels', link, 'createHandler']);

        (new Promise(resolve => {
            if (!handler) {
                resolve({});

                return;
            }

            Espo.loader.requirePromise(handler)
                .then(Handler => new Handler(this.getHelper()))
                .then(handler => {
                    handler.getAttributes(this.model)
                        .then(attributes => resolve(attributes));
                });
        }))
            .then(additionalAttributes => {
                attributes = {...attributes, ...additionalAttributes};

                const viewName = this.getMetadata()
                    .get(['clientDefs', foreignScope, 'modalViews', 'edit']) || 'views/modals/edit';

                this.createView('quickCreate', viewName, {
                    scope: foreignScope,
                    relate: {
                        model: this.model,
                        link: foreignLink,
                    },
                    attributes: attributes,
                }, view => {
                    view.render();
                    view.notify(false);

                    this.listenToOnce(view, 'after:save', () => {
                        this.collection.fetch();

                        this.model.trigger('after:relate');
                        this.model.trigger('after:relate:' + link);
                    });
                });
            });
    }

    
    
    actionUnlinkRelated(data) {
        const id = data.id;

        this.confirm({
            message: this.translate('unlinkRecordConfirmation', 'messages'),
            confirmText: this.translate('Unlink'),
        }, () => {
            Espo.Ui.notify(' ... ');

            Espo.Ajax
                .deleteRequest(this.collection.url, {id: id})
                .then(() => {
                    Espo.Ui.success(this.translate('Unlinked'));

                    this.collection.fetch();

                    this.model.trigger('after:unrelate');
                    this.model.trigger('after:unrelate:' + this.link);
                });
        });
    }

    
    getHeader() {
        const name = this.model.get('name') || this.model.id;

        const recordUrl = '#' + this.scope + '/view/' + this.model.id;

        const $name =
            $('<a>')
                .attr('href', recordUrl)
                .addClass('font-size-flexible title')
                .text(name);

        if (this.model.get('deleted')) {
            $name.css('text-decoration', 'line-through');
        }

        const headerIconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);
        const scopeLabel = this.getLanguage().translate(this.scope, 'scopeNamesPlural');

        let $root = $('<span>').text(scopeLabel);

        if (!this.rootLinkDisabled) {
            $root = $('<span>')
                .append(
                    $('<a>')
                        .attr('href', '#' + this.scope)
                        .addClass('action')
                        .attr('data-action', 'navigateToRoot')
                        .text(scopeLabel)
                );
        }

        if (headerIconHtml) {
            $root.prepend(headerIconHtml);
        }

        const $link = $('<span>').text(this.translate(this.link, 'links', this.scope));

        return this.buildHeaderHtml([
            $root,
            $name,
            $link
        ]);
    }

    
    updatePageTitle() {
        this.setPageTitle(this.getLanguage().translate(this.link, 'links', this.scope));
    }

    
    getCreateAttributes() {}

    
    handleShortcutKeyCtrlSpace(e) {
        if (!this.createButton) {
            return;
        }

        if (!this.getAcl().checkScope(this.foreignScope, 'create')) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();


        this.actionQuickCreate({focusForCreate: true});
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

export default ListRelatedView;
