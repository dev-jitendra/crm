



import ModalView from 'views/modal';
import SearchManager from 'search-manager';


class SelectRecordsModalView extends ModalView {

    template = 'modals/select-records'

    cssName = 'select-modal'
    className = 'dialog dialog-record'
    multiple = false
    createButton = true
    searchPanel = true
    scope = ''
    noCreateScopeList = ['User', 'Team', 'Role', 'Portal']
    layoutName = 'listSmall'

    
    shortcutKeys = {
        
        'Control+Enter': function (e) {
            this.handleShortcutKeyCtrlEnter(e);
        },
        
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

    events = {
        
        'click button[data-action="create"]': function () {
            this.create();
        },
        
        'click .list a': function (e) {
            e.preventDefault();
        },
    }

    data() {
        return {
            createButton: this.createButton,
            createText: this.translate('Create ' + this.scope, 'labels', this.scope),
        };
    }

    setup() {
        
        this.filters = this.options.filters || {};
        this.boolFilterList = this.options.boolFilterList;
        this.primaryFilterName = this.options.primaryFilterName || null;
        this.filterList = this.options.filterList || this.filterList || null;
        this.layoutName = this.options.layoutName || this.layoutName;

        if ('multiple' in this.options) {
            this.multiple = this.options.multiple;
        }

        if ('createButton' in this.options) {
            this.createButton = this.options.createButton;
        }

        this.massRelateEnabled = this.options.massRelateEnabled;

        this.buttonList = [
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        if (this.multiple) {
            this.buttonList.unshift({
                name: 'select',
                style: 'danger',
                label: 'Select',
                disabled: true,
                title: 'Ctrl+Enter',
            });
        }

        this.scope = this.entityType = this.options.scope || this.scope;

        const orderBy = this.options.orderBy ||
            this.getMetadata().get(['clientDefs', this.scope, 'selectRecords', 'orderBy']);

        const order = this.options.orderDirection ||
            this.getMetadata().get(['clientDefs', this.scope, 'selectRecords', 'order']);

        if (orderBy) {
            this.defaultOrderBy = orderBy;
            this.defaultOrder = order || false;
        }

        if (this.noCreateScopeList.indexOf(this.scope) !== -1) {
            this.createButton = false;
        }

        if (this.createButton) {
            if (
                !this.getAcl().check(this.scope, 'create') ||
                this.getMetadata().get(['clientDefs', this.scope, 'createDisabled'])
            ) {
                this.createButton = false;
            }
        }

        if (this.createButton) {
            this.addButton({
                name: 'create',
                position: 'right',
                onClick: () => this.create(),
                iconClass: 'fas fa-plus fa-sm',
                label: 'Create',
            });
        }

        if (this.getMetadata().get(['clientDefs', this.scope, 'searchPanelDisabled'])) {
            this.searchPanel = false;
        }

        if (this.getUser().isPortal()) {
            if (this.getMetadata().get(['clientDefs', this.scope, 'searchPanelInPortalDisabled'])) {
                this.searchPanel = false;
            }
        }

        this.$header = $('<span>');

        this.$header.append(
            $('<span>').text(
                this.translate('Select') + ' · ' +
                this.getLanguage().translate(this.scope, 'scopeNamesPlural')
            )
        );

        this.$header.prepend(
            this.getHelper().getScopeColorIconHtml(this.scope)
        );

        this.waitForView('list');

        if (this.searchPanel) {
            this.waitForView('search');
        }

        this.getCollectionFactory().create(this.scope, (collection) => {
            collection.maxSize = this.getConfig().get('recordsPerPageSelect') || 5;

            this.collection = collection;

            if (this.defaultOrderBy) {
                this.collection.setOrder(this.defaultOrderBy, this.defaultOrder || 'asc', true);
            }

            this.setupSearch();
            this.setupList();
        });

        
        this.once('close', () => {
            if (
                this.collection.lastSyncPromise &&
                this.collection.lastSyncPromise.getStatus() < 4
            ) {
                Espo.Ui.notify(false);
            }

            this.collection.abortLastFetch();
        });
    }

    setupSearch() {
        const searchManager = this.searchManager =
            new SearchManager(this.collection, 'listSelect', null, this.getDateTime());

        searchManager.emptyOnReset = true;

        if (this.filters) {
            searchManager.setAdvanced(this.filters);
        }

        const boolFilterList = this.boolFilterList ||
            this.getMetadata().get('clientDefs.' + this.scope + '.selectDefaultFilters.boolFilterList');

        if (boolFilterList) {
            const d = {};

            boolFilterList.forEach(item => {
                d[item] = true;
            });

            searchManager.setBool(d);
        }

        const primaryFilterName = this.primaryFilterName ||
            this.getMetadata().get('clientDefs.' + this.scope + '.selectDefaultFilters.filter');

        if (primaryFilterName) {
            searchManager.setPrimary(primaryFilterName);
        }

        this.collection.where = searchManager.getWhere();

        if (this.searchPanel) {
            this.createView('search', 'views/record/search', {
                collection: this.collection,
                fullSelector: this.containerSelector + ' .search-container',
                searchManager: searchManager,
                disableSavePreset: true,
                filterList: this.filterList,
            }, view => {
                this.listenTo(view, 'reset', () => {});
            });
        }
    }

    setupList() {
        const viewName = this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.listSelect') ||
            this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.list') ||
            'views/record/list';

        const promise = this.createView('list', viewName, {
            collection: this.collection,
            fullSelector: this.containerSelector + ' .list-container',
            selectable: true,
            checkboxes: this.multiple,
            massActionsDisabled: true,
            rowActionsView: false,
            layoutName: this.layoutName,
            searchManager: this.searchManager,
            checkAllResultDisabled: !this.massRelateEnabled,
            buttonsDisabled: true,
            skipBuildRows: true,
            pagination: this.getMetadata().get(['clientDefs', this.scope, 'listPagination']) || null,
        }, view => {

            this.listenToOnce(view, 'select', model => {
                this.trigger('select', model);

                this.close();
            });

            if (this.multiple) {
                this.listenTo(view, 'check', () => {
                    if (view.checkedList.length) {
                        this.enableButton('select');
                    }
                    else {
                        this.disableButton('select');
                    }
                });

                this.listenTo(view, 'select-all-results', () => {
                    this.enableButton('select');
                });
            }

            const fetch = () => {
                this.whenRendered().then(() => {
                    Espo.Ui.notify(' ... ');

                    this.collection.fetch()
                        .then(() => Espo.Ui.notify(false));
                });
                
                
            };

            if (this.options.forceSelectAllAttributes || this.forceSelectAllAttributes) {
                fetch();

                return;
            }

            view.getSelectAttributeList(selectAttributeList => {
                if (!~selectAttributeList.indexOf('name')) {
                    selectAttributeList.push('name');
                }

                const mandatorySelectAttributeList = this.options.mandatorySelectAttributeList ||
                    this.mandatorySelectAttributeList || [];

                mandatorySelectAttributeList.forEach(attribute => {
                    if (!~selectAttributeList.indexOf(attribute)) {
                        selectAttributeList.push(attribute);
                    }
                });

                if (selectAttributeList) {
                    this.collection.data.select = selectAttributeList.join(',');
                }

                fetch();
            });
        });

        this.wait(promise);
    }

    create() {
        if (this.options.triggerCreateEvent) {
            this.trigger('create');

            return;
        }

        Espo.Ui.notify(' ... ');

        const viewName = this.getMetadata()
                .get(['clientDefs', this.scope, 'modalViews', 'edit']) ||
            'views/modals/edit';

        new Promise(resolve => {
            if (this.options.createAttributesProvider) {
                this.options.createAttributesProvider().then(attributes => {
                    resolve(attributes)
                });

                return;
            }

            resolve(this.options.createAttributes || {});
        })
            .then(attributes => {
                this.createView('quickCreate', viewName, {
                    scope: this.scope,
                    fullFormDisabled: true,
                    attributes: attributes,
                }, view => {
                    view.render()
                        .then(() => Espo.Ui.notify(false));

                    this.listenToOnce(view, 'leave', () => {
                        view.close();
                        this.close();
                    });

                    this.listenToOnce(view, 'after:save', (model) => {
                        view.close();

                        this.trigger('select', model);

                        setTimeout(() => this.close(), 10);
                    });
                });
            });
    }

    actionSelect() {
        if (!this.multiple) {
            return;
        }

        const listView = this.getRecordView();

        if (listView.allResultIsChecked) {
            this.trigger('select', {
                massRelate: true,
                where: this.collection.getWhere(),
                searchParams: this.collection.data,
            });

            this.close();

            return;
        }

        const list = listView.getSelected();

        if (list.length) {
            this.trigger('select', list);
        }

        this.close();
    }

    
    getSearchView() {
        return this.getView('search');
    }

    
    getRecordView() {
        return this.getView('list');
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

    
    handleShortcutKeyCtrlEnter(e) {
        if (!this.multiple) {
            return;
        }

        if (!this.hasAvailableActionItem('select')) {
            return;
        }

        e.stopPropagation();
        e.preventDefault();

        this.actionSelect();
    }

    
    handleShortcutKeyCtrlSpace(e) {
        if (!this.createButton) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        this.create();
    }

    
    handleShortcutKeyCtrlComma() {
        if (!this.getSearchView()) {
            return;
        }

        this.getSearchView().selectPreviousPreset();
    }

    
    handleShortcutKeyCtrlPeriod() {
        if (!this.getSearchView()) {
            return;
        }

        this.getSearchView().selectNextPreset();
    }
}

export default SelectRecordsModalView;
