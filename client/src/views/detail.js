



import MainView from 'views/main';


class DetailView extends MainView {

    
    template = 'detail'
    
    name = 'Detail'

    
    optionsToPass = [
        'attributes',
        'returnUrl',
        'returnDispatchParams',
        'rootUrl',
    ]

    
    headerView = 'views/header'

    
    recordView = 'views/record/detail'

    
    rootLinkDisabled = false

    
    rootUrl = ''

    
    isReturn = false

    
    shortcutKeys = {}

    
    entityType

    
    setup() {
        super.setup();

        this.entityType = this.model.entityType || this.model.name;

        this.headerView = this.options.headerView || this.headerView;
        this.recordView = this.options.recordView || this.recordView;

        this.rootUrl = this.options.rootUrl || this.options.params.rootUrl || '#' + this.scope;
        this.isReturn = this.options.isReturn || this.options.params.isReturn || false;

        this.setupHeader();
        this.setupRecord();
        this.setupPageTitle();
        this.initFollowButtons();
        this.initRedirect();
    }

    
    setupFinal() {
        super.setupFinal();

        this.wait(
            this.getHelper().processSetupHandlers(this, 'detail')
        );
    }

    
    initRedirect() {
        if (!this.options.params.isAfterCreate) {
            return;
        }

        const redirect = () => {
            Espo.Ui.success(this.translate('Created'));

            setTimeout(() => {
                this.getRouter().navigate(this.rootUrl, {trigger: true});
            }, 1000)
        };

        if (
            this.model.lastSyncPromise &&
            this.model.lastSyncPromise.getStatus() === 403
        ) {
            redirect();

            return;
        }

        this.listenToOnce(this.model, 'fetch-forbidden', () => redirect())
    }

    
    setupPageTitle() {
        this.listenTo(this.model, 'after:save', () => {
            this.updatePageTitle();
        });

        this.listenTo(this.model, 'sync', (model) => {
            if (model && model.hasChanged('name')) {
                this.updatePageTitle();
            }
        });
    }

    
    setupHeader() {
        this.createView('header', this.headerView, {
            model: this.model,
            fullSelector: '#main > .header',
            scope: this.scope,
            fontSizeFlexible: true,
        });

        this.listenTo(this.model, 'sync', (model) => {
            if (model && model.hasChanged('name')) {
                if (this.getView('header')) {
                    this.getView('header').reRender();
                }
            }
        });
    }

    
    setupRecord() {
        const o = {
            model: this.model,
            fullSelector: '#main > .record',
            scope: this.scope,
            shortcutKeysEnabled: true,
            isReturn: this.isReturn,
        };

        this.optionsToPass.forEach((option) => {
            o[option] = this.options[option];
        });

        if (this.options.params && this.options.params.rootUrl) {
            o.rootUrl = this.options.params.rootUrl;
        }

        if (this.model.get('deleted')) {
            o.readOnly = true;
        }

        return this.createView('record', this.getRecordViewName(), o, view => {
            this.listenTo(view, 'after:mode-change', () => this.getHeaderView().reRender());
        });
    }

    
    getRecordViewName() {
        return this.getMetadata()
            .get('clientDefs.' + this.scope + '.recordViews.detail') || this.recordView;
    }

    
    initFollowButtons() {
        if (!this.getMetadata().get(['scopes', this.scope, 'stream'])) {
            return;
        }

        this.addFollowButtons();

        this.listenTo(this.model, 'change:isFollowed', () => {
            this.controlFollowButtons();
        });
    }

    
    addFollowButtons() {
        const isFollowed = this.model.get('isFollowed');

        this.addMenuItem('buttons', {
            name: 'unfollow',
            label: 'Followed',
            style: 'success',
            action: 'unfollow',
            hidden: !isFollowed,
        }, true);

        this.addMenuItem('buttons', {
            name: 'follow',
            label: 'Follow',
            style: 'default',
            iconHtml: '<span class="fas fa-rss fa-sm"></span>',
            text: this.translate('Follow'),
            action: 'follow',
            hidden: isFollowed ||
                !this.model.has('isFollowed') ||
                !this.getAcl().checkModel(this.model, 'stream'),
        }, true);
    }

    
    controlFollowButtons() {
        const isFollowed = this.model.get('isFollowed');

        if (isFollowed) {
            this.hideHeaderActionItem('follow');
            this.showHeaderActionItem('unfollow');

            return;
        }

        this.hideHeaderActionItem('unfollow');

        if (this.getAcl().checkModel(this.model, 'stream')) {
            this.showHeaderActionItem('follow');
        }
    }

    
    
    actionFollow() {
        this.disableMenuItem('follow');

        Espo.Ajax
            .putRequest(this.entityType + '/' + this.model.id + '/subscription')
            .then(() => {
                this.hideHeaderActionItem('follow');

                this.model.set('isFollowed', true, {sync: true});

                this.enableMenuItem('follow');
            })
            .catch(() => {
                this.enableMenuItem('follow');
            });
    }

    
    
    actionUnfollow() {
        this.disableMenuItem('unfollow');

        Espo.Ajax
            .deleteRequest(this.entityType + '/' + this.model.id + '/subscription')
            .then(() => {
                this.hideHeaderActionItem('unfollow');

                this.model.set('isFollowed', false, {sync: true});

                this.enableMenuItem('unfollow');
            })
            .catch(() => {
                this.enableMenuItem('unfollow');
            });
    }

    
    getHeader() {
        const name = this.model.get('name') || this.model.id;

        const $name =
            $('<span>')
                .addClass('font-size-flexible title')
                .text(name);

        if (this.model.get('deleted')) {
            $name.css('text-decoration', 'line-through');
        }

        const headerIconHtml = this.getHeaderIconHtml();
        const scopeLabel = this.getLanguage().translate(this.scope, 'scopeNamesPlural');

        let $root = $('<span>').text(scopeLabel);

        if (!this.rootLinkDisabled) {
            $root = $('<span>')
                .append(
                    $('<a>')
                        .attr('href', this.rootUrl)
                        .addClass('action')
                        .attr('data-action', 'navigateToRoot')
                        .text(scopeLabel)
                );
        }

        if (headerIconHtml) {
            $root.prepend(headerIconHtml);
        }

        return this.buildHeaderHtml([
            $root,
            $name,
        ]);
    }

    
    updatePageTitle() {
        if (this.model.has('name')) {
            this.setPageTitle(this.model.get('name') || this.model.id);

            return;
        }

        super.updatePageTitle();
    }

    
    getRecordView() {
        return this.getView('record');
    }

    
    updateRelationshipPanel(name) {
        const bottom = this.getView('record').getView('bottom');

        if (bottom) {
            const rel = bottom.getView(name);

            if (rel) {
                rel.collection.fetch();
            }
        }
    }

    
    relatedAttributeMap = {}

    
    relatedAttributeFunctions = {}

    
    selectRelatedFilters = {}

    
    selectPrimaryFilterNames = {}

    
    selectBoolFilterLists = []

    
    actionCreateRelated(data) {
        data = data || {};

        const link = data.link;
        const scope = this.model.defs['links'][link].entity;
        const foreignLink = this.model.defs['links'][link].foreign;

        let attributes = {};

        if (
            this.relatedAttributeFunctions[link] &&
            typeof this.relatedAttributeFunctions[link] === 'function'
        ) {
            attributes = _.extend(this.relatedAttributeFunctions[link].call(this), attributes);
        }

        const attributeMap = this.getMetadata()
                .get(['clientDefs', this.scope, 'relationshipPanels', link, 'createAttributeMap']) ||
            this.relatedAttributeMap[link] || {};

        Object.keys(attributeMap)
            .forEach(attr => {
                attributes[attributeMap[attr]] = this.model.get(attr);
            });

        Espo.Ui.notify(' ... ');

        const handler = this.getMetadata()
            .get(['clientDefs', this.scope, 'relationshipPanels', link, 'createHandler']);

        new Promise(resolve => {
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
        }).then(additionalAttributes => {
            attributes = {...attributes, ...additionalAttributes};

            const viewName = this.getMetadata()
                .get(['clientDefs', scope, 'modalViews', 'edit']) || 'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: scope,
                relate: {
                    model: this.model,
                    link: foreignLink,
                },
                attributes: attributes,
            }, view => {
                view.render();
                view.notify(false);

                this.listenToOnce(view, 'after:save', () => {
                    if (data.fromSelectRelated) {
                        setTimeout(() => this.clearView('dialogSelectRelated'), 25);
                    }

                    this.updateRelationshipPanel(link);

                    this.model.trigger('after:relate');
                    this.model.trigger('after:relate:' + link);
                });
            });
        });
    }

    
    
    actionSelectRelated(data) {
        const link = data.link;

        if (!data.foreignEntityType && !this.model.defs['links'][link]) {
            throw new Error('Link ' + link + ' does not exist.');
        }

        const scope = data.foreignEntityType || this.model.defs['links'][link].entity;
        const massRelateEnabled = data.massSelect;

        
        const panelDefs = this.getMetadata().get(['clientDefs', this.scope, 'relationshipPanels', link]) || {};

        let advanced = {};

        if (link in this.selectRelatedFilters) {
            advanced = Espo.Utils.cloneDeep(this.selectRelatedFilters[link]) || advanced;

            for (const filterName in advanced) {
                if (typeof advanced[filterName] === 'function') {
                    const filtersData = advanced[filterName].call(this);

                    if (filtersData) {
                        advanced[filterName] = filtersData;
                    } else {
                        delete advanced[filterName];
                    }
                }
            }
        }

        const foreignLink = this.model.getLinkParam(link, 'foreign');

        if (foreignLink && scope) {
            
            const foreignLinkType = this.getMetadata()
                .get(['entityDefs', scope, 'links', foreignLink, 'type']);
            const foreignLinkFieldType = this.getMetadata()
                .get(['entityDefs', scope, 'fields', foreignLink, 'type']);

            if (
                ~['belongsTo', 'belongsToParent'].indexOf(foreignLinkType) &&
                foreignLinkFieldType &&
                !advanced[foreignLink] &&
                ~['link', 'linkParent'].indexOf(foreignLinkFieldType)
            ) {
                advanced[foreignLink] = {
                    type: 'isNull',
                    attribute: foreignLink + 'Id',
                    data: {
                        type: 'isEmpty',
                    },
                };
            }
        }

        let primaryFilterName = data.primaryFilterName || this.selectPrimaryFilterNames[link] || null;

        if (typeof primaryFilterName === 'function') {
            primaryFilterName = primaryFilterName.call(this);
        }

        let dataBoolFilterList = data.boolFilterList;

        if (typeof data.boolFilterList === 'string') {
            dataBoolFilterList = data.boolFilterList.split(',');
        }

        let boolFilterList = dataBoolFilterList ||
            panelDefs.selectBoolFilterList ||
            this.selectBoolFilterLists[link];

        if (typeof boolFilterList === 'function') {
            boolFilterList = boolFilterList.call(this);
        }

        boolFilterList = Espo.Utils.clone(boolFilterList);

        primaryFilterName = primaryFilterName || panelDefs.selectPrimaryFilterName || null;

        const viewKey = data.viewKey || 'select';

        const viewName = panelDefs.selectModalView ||
            this.getMetadata().get(['clientDefs', scope, 'modalViews', viewKey]) ||
            'views/modals/select-records';

        Espo.Ui.notify(' ... ');

        const handler = panelDefs.selectHandler || null;

        new Promise(resolve => {
            if (!handler) {
                resolve({});

                return;
            }

            Espo.loader.requirePromise(handler)
                .then(Handler => new Handler(this.getHelper()))
                .then(handler => {
                    handler.getFilters(this.model)
                        .then(filters => resolve(filters));
                });
        }).then(filters => {
            advanced = {...advanced, ...(filters.advanced || {})};

            if (boolFilterList || filters.bool) {
                boolFilterList = [
                    ...(boolFilterList || []),
                    ...(filters.bool || []),
                ];
            }

            if (filters.primary && !primaryFilterName) {
                primaryFilterName = filters.primary;
            }

            this.createView('dialogSelectRelated', viewName, {
                scope: scope,
                multiple: true,
                createButton: data.createButton || false,
                triggerCreateEvent: true,
                filters: advanced,
                massRelateEnabled: massRelateEnabled,
                primaryFilterName: primaryFilterName,
                boolFilterList: boolFilterList,
                mandatorySelectAttributeList: panelDefs.selectMandatoryAttributeList,
                layoutName: panelDefs.selectLayout,
            }, dialog => {
                dialog.render();

                Espo.Ui.notify(false);

                this.listenTo(dialog, 'create', () => {
                    this.actionCreateRelated({
                        link: data.link,
                        fromSelectRelated: true,
                    });
                });

                this.listenToOnce(dialog, 'select', (selectObj) => {
                    const data = {};

                    if (Object.prototype.toString.call(selectObj) === '[object Array]') {
                        const ids = [];

                        selectObj.forEach(model => ids.push(model.id));

                        data.ids = ids;
                    }
                    else {
                        if (selectObj.massRelate) {
                            data.massRelate = true;
                            data.where = selectObj.where;
                            data.searchParams = selectObj.searchParams;
                        }
                        else {
                            data.id = selectObj.id;
                        }
                    }

                    const url = this.scope + '/' + this.model.id + '/' + link;

                    Espo.Ajax.postRequest(url, data)
                        .then(() => {
                            Espo.Ui.success(this.translate('Linked'))

                            this.updateRelationshipPanel(link);

                            this.model.trigger('after:relate');
                            this.model.trigger('after:relate:' + link);
                        });
                });
            });
        });
    }

    
    
    actionDuplicate() {
        Espo.Ui.notify(' ... ');

        Espo.Ajax
            .postRequest(this.scope + '/action/getDuplicateAttributes', {id: this.model.id})
            .then(attributes => {
                Espo.Ui.notify(false);

                const url = '#' + this.scope + '/create';

                this.getRouter().dispatch(this.scope, 'create', {
                    attributes: attributes,
                    returnUrl: this.getRouter().getCurrentUrl(),
                    options: {
                        duplicateSourceId: this.model.id,
                    },
                });

                this.getRouter().navigate(url, {trigger: false});
            });
    }
}

export default DetailView;
