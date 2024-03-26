



import BottomPanelView from 'views/record/panels/bottom';
import SearchManager from 'search-manager';
import RecordModal from 'helpers/record-modal';


class RelationshipPanelView extends BottomPanelView {

    
    template = 'record/panels/relationship'

    
    rowActionsView = 'views/record/row-actions/relationship'

    
    url = null

    
    scope

    
    entityType

    
    readOnly = false

    
    fetchOnModelAfterRelate = false

    
    noCreateScopeList = ['User', 'Team', 'Role', 'Portal']

    
    recordsPerPage = null

    
    viewModalView = null

    setup() {
        super.setup();

        this.link = this.link || this.defs.link || this.panelName;

        if (!this.link) {
            throw new Error(`No link or panelName.`);
        }

        
        if (!this.scope && !this.entityType) {
            if (!this.model) {
                throw new Error(`No model passed.`);
            }

            if (!(this.link in this.model.defs.links)) {
                throw new Error(`Link '${this.link}' is not defined in model '${this.model.entityType}'.`);
            }
        }

        
        if (this.scope && !this.entityType) {
            
            
            this.entityType = this.scope;
        }

        this.entityType = this.entityType || this.model.defs.links[this.link].entity;

        
        
        this.scope = this.entityType;

        const linkReadOnly = this.getMetadata()
            .get(['entityDefs', this.model.entityType, 'links', this.link, 'readOnly']) || false;

        const url = this.url = this.url || this.model.entityType + '/' + this.model.id + '/' + this.link;

        if (!('create' in this.defs)) {
            this.defs.create = true;
        }

        if (!('select' in this.defs)) {
            this.defs.select = true;
        }

        if (!('view' in this.defs)) {
            this.defs.view = true;
        }

        if (linkReadOnly) {
            this.defs.create = false;
            this.defs.select = false;
        }

        this.filterList = this.defs.filterList || this.filterList || null;

        if (this.filterList && this.filterList.length) {
            this.filter = this.getStoredFilter() || this.filterList[0];

            if (this.filter === 'all') {
                this.filter = null;
            }
        }

        this.setupCreateAvailability();

        this.setupTitle();

        if (this.defs.createDisabled) {
            this.defs.create = false;
        }

        if (this.defs.selectDisabled) {
            this.defs.select = false;
        }

        if (this.defs.viewDisabled) {
            this.defs.view = false;
        }

        let hasCreate = false;

        if (this.defs.create) {
            if (
                this.getAcl().check(this.entityType, 'create') &&
                !~this.noCreateScopeList.indexOf(this.entityType)
            ) {
                this.buttonList.push({
                    title: 'Create',
                    action: this.defs.createAction || 'createRelated',
                    link: this.link,
                    html: '<span class="fas fa-plus"></span>',
                    data: {
                        link: this.link,
                    },
                    acl: this.defs.createRequiredAccess || null,
                });

                hasCreate = true;
            }
        }

        if (this.defs.select) {
            const data = {link: this.link};

            if (this.defs.selectPrimaryFilterName) {
                data.primaryFilterName = this.defs.selectPrimaryFilterName;
            }

            if (this.defs.selectBoolFilterList) {
                data.boolFilterList = this.defs.selectBoolFilterList;
            }

            data.massSelect = this.defs.massSelect;
            data.createButton = hasCreate;

            this.actionList.unshift({
                label: 'Select',
                action: this.defs.selectAction || 'selectRelated',
                data: data,
                acl: this.defs.selectRequiredAccess || 'edit',
            });
        }

        if (this.defs.view) {
            this.actionList.unshift({
                label: 'View List',
                action: this.defs.viewAction || 'viewRelatedList',
            });
        }

        this.setupActions();

        let layoutName = 'listSmall';

        this.setupListLayout();

        if (this.listLayoutName) {
            layoutName = this.listLayoutName;
        }

        let listLayout = null;

        const layout = this.defs.layout || null;

        if (layout) {
            if (typeof layout === 'string') {
                 layoutName = layout;
            } else {
                 layoutName = 'listRelationshipCustom';
                 listLayout = layout;
            }
        }

        this.listLayout = listLayout;
        this.layoutName = layoutName;

        this.setupSorting();

        this.wait(true);

        this.getCollectionFactory().create(this.entityType, collection => {
            collection.maxSize = this.recordsPerPage || this.getConfig().get('recordsPerPageSmall') || 5;

            if (this.defs.filters) {
                const searchManager = new SearchManager(collection, 'listRelationship', null, this.getDateTime());

                searchManager.setAdvanced(this.defs.filters);
                collection.where = searchManager.getWhere();
            }

            if (this.defs.primaryFilter) {
                this.filter = this.defs.primaryFilter;
            }

            collection.url = collection.urlRoot = url;

            if (this.defaultOrderBy) {
                collection.setOrder(this.defaultOrderBy, this.defaultOrder || false, true);
            }

            this.collection = collection;

            collection.parentModel = this.model;

            this.setFilter(this.filter);

            if (this.fetchOnModelAfterRelate) {
                this.listenTo(this.model, 'after:relate', () => collection.fetch());
            }

            this.listenTo(this.model, 'update-all', () => collection.fetch());

            if (this.defs.syncWithModel) {
                this.listenTo(this.model, 'sync', (m, a, o) => {
                    if (!o.patch && !o.highlight) {
                        
                        return;
                    }

                    if (
                        this.collection.lastSyncPromise &&
                        this.collection.lastSyncPromise.getReadyState() < 4
                    ) {
                        return;
                    }

                    this.collection.fetch();
                });
            }

            const viewName =
                this.defs.recordListView ||
                this.getMetadata().get(['clientDefs', this.entityType, 'recordViews', 'listRelated']) ||
                this.getMetadata().get(['clientDefs', this.entityType, 'recordViews', 'list']) ||
                'views/record/list';

            this.listViewName = viewName;
            this.rowActionsView = this.defs.readOnly ? false : (this.defs.rowActionsView || this.rowActionsView);

            this.once('after:render', () => {
                this.createView('list', viewName, {
                    collection: collection,
                    layoutName: layoutName,
                    listLayout: listLayout,
                    checkboxes: false,
                    rowActionsView: this.rowActionsView,
                    buttonsDisabled: true,
                    selector: '.list-container',
                    skipBuildRows: true,
                    rowActionsOptions: {
                        unlinkDisabled: this.defs.unlinkDisabled,
                        editDisabled: this.defs.editDisabled,
                        removeDisabled: this.defs.removeDisabled,
                    },
                    displayTotalCount: false,
                    additionalRowActionList: this.defs.rowActionList,
                }, view => {
                    view.getSelectAttributeList((selectAttributeList) => {
                        if (selectAttributeList) {
                            collection.data.select = selectAttributeList.join(',');
                        }

                        if (!this.defs.hidden) {
                            collection.fetch();

                            return;
                        }

                        this.once('show', () => collection.fetch());
                    });
                });
            });

            this.wait(false);
        });

        this.setupFilterActions();
        this.setupLast();
    }

    
    setupLast() {}

    
    setupTitle() {
        this.title = this.title || this.translate(this.link, 'links', this.model.entityType);

        let iconHtml = '';

        if (!this.getConfig().get('scopeColorsDisabled')) {
            iconHtml = this.getHelper().getScopeColorIconHtml(this.entityType);
        }

        this.titleHtml = this.title;

        if (this.defs.label) {
            this.titleHtml = iconHtml + this.translate(this.defs.label, 'labels', this.entityType);
        } else {
            this.titleHtml = iconHtml + this.title;
        }

        if (this.filter && this.filter !== 'all') {
            this.titleHtml += ' &middot; ' + this.translateFilter(this.filter);
        }
    }

    
    setupSorting() {
        let orderBy = this.defs.orderBy || this.defs.sortBy || this.orderBy;
        let order = this.defs.orderDirection || this.orderDirection || this.order;

        if ('asc' in this.defs) { 
            order = this.defs.asc ? 'asc' : 'desc';
        }

        if (!orderBy) {
            orderBy = this.getMetadata().get(['entityDefs', this.entityType, 'collection', 'orderBy']);
            order = this.getMetadata().get(['entityDefs', this.entityType, 'collection', 'order'])
        }

        if (orderBy && !order) {
            order = 'asc';
        }

        this.defaultOrderBy = orderBy;
        this.defaultOrder = order;
    }

    
    setupListLayout() {}

    
    setupActions() {}

    
    setupFilterActions() {
        if (!(this.filterList && this.filterList.length)) {
            return;
        }

        this.actionList.push(false);

        this.filterList.slice(0).forEach((item) => {
            let selected;

            selected = item === 'all' ?
                !this.filter :
                item === this.filter;

            const label = this.translateFilter(item);

            const $item =
                $('<div>')
                    .append(
                        $('<span>')
                            .addClass('check-icon fas fa-check pull-right')
                            .addClass(!selected ? 'hidden' : '')
                    )
                    .append(
                        $('<div>').text(label)
                    );

            this.actionList.push({
                action: 'selectFilter',
                html: $item.get(0).innerHTML,
                data: {
                    name: item,
                },
            });
        });
    }

    
    translateFilter(name) {
        return this.translate(name, 'presetFilters', this.entityType);
    }

    
    getStoredFilter() {
        const key = 'panelFilter' + this.model.entityType + '-' + (this.panelName || this.name);

        return this.getStorage().get('state', key) || null;
    }

    
    storeFilter(filter) {
        const key = 'panelFilter' + this.model.entityType + '-' + (this.panelName || this.name);

        if (filter) {
            this.getStorage().set('state', key, filter);
        } else {
            this.getStorage().clear('state', key);
        }
    }

    
    setFilter(filter) {
        this.filter = filter;
        this.collection.data.primaryFilter = null;

        if (filter && filter !== 'all') {
            this.collection.data.primaryFilter = filter;
        }
    }

    
    
    actionSelectFilter(data) {
        const filter = data.name;
        let filterInternal = filter;

        if (filter === 'all') {
            filterInternal = false;
        }

        this.storeFilter(filterInternal);
        this.setFilter(filterInternal);

        this.filterList.forEach(item => {
            const $el = this.$el.closest('.panel').find('[data-name="' + item + '"] span');

            if (item === filter) {
                $el.removeClass('hidden');
            } else {
                $el.addClass('hidden');
            }
        });

        this.collection.reset();

        const listView = this.getView('list');

        if (listView && listView.$el) {
            const height = listView.$el.parent().get(0).clientHeight;

            listView.$el.empty();

            if (height) {
                listView.$el.parent().css('height', height + 'px');
            }
        }

        this.collection.fetch().then(() => {
            listView.$el.parent().css('height', '');
        });

        this.setupTitle();

        if (this.isRendered()) {
            this.$el.closest('.panel')
                .find('> .panel-heading > .panel-title > span')
                .html(this.titleHtml);
        }
    }

    
    actionRefresh() {
        this.collection.fetch();
    }

    
    actionViewRelatedList(data) {
        const viewName =
            this.getMetadata().get(
                ['clientDefs', this.model.entityType, 'relationshipPanels', this.name, 'viewModalView']
            ) ||
            this.getMetadata().get(['clientDefs', this.entityType, 'modalViews', 'relatedList']) ||
            this.viewModalView ||
            'views/modals/related-list';

        const scope = data.scope || this.entityType;

        let filter = this.filter;

        if (this.relatedListFiltersDisabled) {
            filter = null;
        }

        const options = {
            model: this.model,
            panelName: this.panelName,
            link: this.link,
            scope: scope,
            defs: this.defs,
            title: data.title || this.title,
            filterList: this.filterList,
            filter: filter,
            layoutName: this.layoutName,
            defaultOrder: this.defaultOrder,
            defaultOrderBy: this.defaultOrderBy,
            url: data.url || this.url,
            listViewName: this.listViewName,
            createDisabled: !this.isCreateAvailable(scope),
            selectDisabled: !this.isSelectAvailable(scope),
            rowActionsView: this.rowActionsView,
            panelCollection: this.collection,
            filtersDisabled: this.relatedListFiltersDisabled,
        };

        if (data.viewOptions) {
            for (const item in data.viewOptions) {
                options[item] = data.viewOptions[item];
            }
        }

        Espo.Ui.notify(' ... ');

        this.createView('modalRelatedList', viewName, options, view => {
            Espo.Ui.notify(false);

            view.render();

            this.listenTo(view, 'action', (event, element) => {
                Espo.Utils.handleAction(this, event, element);
            });

            this.listenToOnce(view, 'close', () => {
                this.clearView('modalRelatedList');
            });
        });
    }

    
    isCreateAvailable(scope) {
        return !!this.defs.create;
    }

    
    
    isSelectAvailable(scope) {
        return !!this.defs.select;
    }

    
    
    actionViewRelated(data) {
        const id = data.id;
        const model = this.collection.get(id);

        if (!model) {
            return;
        }

        const scope = model.entityType;

        const helper = new RecordModal(this.getMetadata(), this.getAcl());

        helper
            .showDetail(this, {
                scope: scope,
                id: id,
                model: model,
            })
            .then(view => {
                this.listenTo(view, 'after:save', () => {
                    this.collection.fetch();
                });
            });
    }

    
    
    actionEditRelated(data) {
        const id = data.id;
        const scope = this.collection.get(id).name;

        const viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.edit') ||
            'views/modals/edit';

        Espo.Ui.notify(' ... ');

        this.createView('quickEdit', viewName, {
            scope: scope,
            id: id,
        }, (view) => {
            view.once('after:render', () => {
                Espo.Ui.notify(false);
            });

            view.render();

            view.once('after:save', () => {
                this.collection.fetch();
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

    
    
    actionRemoveRelated(data) {
        const id = data.id;

        this.confirm({
            message: this.translate('removeRecordConfirmation', 'messages'),
            confirmText: this.translate('Remove'),
        }, () => {
            const model = this.collection.get(id);

            Espo.Ui.notify(' ... ');

            model
                .destroy()
                .then(() => {
                    Espo.Ui.success(this.translate('Removed'));

                    this.collection.fetch();

                    this.model.trigger('after:unrelate');
                    this.model.trigger('after:unrelate:' + this.link);
                });
        });
    }

    
    
    actionUnlinkAllRelated(data) {
        this.confirm(this.translate('unlinkAllConfirmation', 'messages'), () => {
            Espo.Ui.notify(' ... ');

            Espo.Ajax
                .postRequest(this.model.entityType + '/action/unlinkAll', {
                    link: data.link,
                    id: this.model.id,
                })
                .then(() => {
                    Espo.Ui.success(this.translate('Unlinked'));

                    this.collection.fetch();

                    this.model.trigger('after:unrelate');
                    this.model.trigger('after:unrelate:' + this.link);
                });
        });
    }

    
    setupCreateAvailability() {
        if (!this.link || !this.entityType || !this.model) {
            return;
        }

        
        const model = this.model;

        const entityType = model.getLinkParam(this.link, 'entity');
        const foreignLink = model.getLinkParam(this.link, 'foreign');

        if (!entityType || !foreignLink) {
            return;
        }

        const readOnly = this.getMetadata().get(`entityDefs.${entityType}.fields.${foreignLink}.readOnly`);

        if (!readOnly) {
            return;
        }

        this.defs.create = false;
    }
}

export default RelationshipPanelView;
