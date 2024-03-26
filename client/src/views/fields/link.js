



import BaseFieldView from 'views/fields/base';
import RecordModal from 'helpers/record-modal';


class LinkFieldView extends BaseFieldView {

    
    type = 'link'

    
    listTemplate = 'fields/link/list'
    
    detailTemplate = 'fields/link/detail'
    
    editTemplate = 'fields/link/edit'
    
    searchTemplate = 'fields/link/search'

    
    nameName

    
    idName

    
    foreignScope = null

    
    selectRecordsView = 'views/modals/select-records'

    
    autocompleteDisabled = false

    
    createDisabled = false

    
    createButton = false

    
    forceCreateButton = false

    
    searchTypeList = [
        'is',
        'isEmpty',
        'isNotEmpty',
        'isNot',
        'isOneOf',
        'isNotOneOf',
    ]

    
    selectFilterList = null

    
    selectPrimaryFilterName = null

    
    selectBoolFilterList = null

    
    autocompleteMaxCount = null

    
    forceSelectAllAttributes = false

    
    mandatorySelectAttributeList = null

    
    autocompleteOnEmpty = false

    
    events = {
        
        'auxclick a[href]:not([role="button"])': function (e) {
            if (!this.isReadMode()) {
                return;
            }

            const isCombination = e.button === 1 && (e.ctrlKey || e.metaKey);

            if (!isCombination) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.quickView();
        },
    }

    
    
    data() {
        let nameValue = this.model.has(this.nameName) ?
            this.model.get(this.nameName) :
            this.model.get(this.idName);

        if (nameValue === null) {
            nameValue = this.model.get(this.idName);
        }

        if (this.isReadMode() && !nameValue && this.model.get(this.idName)) {
            nameValue = this.translate(this.foreignScope, 'scopeNames');
        }

        let iconHtml = null;

        if (this.isDetailMode()) {
            iconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);
        }

        const createButton = this.createButton && (!this.createDisabled || this.forceCreateButton);

        
        return {
            ...super.data(),
            idName: this.idName,
            nameName: this.nameName,
            idValue: this.model.get(this.idName),
            nameValue: nameValue,
            foreignScope: this.foreignScope,
            valueIsSet: this.model.has(this.idName),
            iconHtml: iconHtml,
            url: this.getUrl(),
            createButton: createButton,
        };
    }

    
    getUrl() {
        const id = this.model.get(this.idName);

        if (!id) {
            return null;
        }

        return '#' + this.foreignScope + '/view/' + id;
    }

    
    getSelectFilters() {
        return null;
    }

    
    getSelectBoolFilterList() {
        return this.selectBoolFilterList;
    }

    
    getSelectPrimaryFilterName() {
        return this.selectPrimaryFilterName;
    }

    
    getSelectFilterList() {
        return this.selectFilterList;
    }

    
    getCreateAttributes() {
        const attributeMap = this.getMetadata()
            .get(['clientDefs', this.entityType, 'relationshipPanels', this.name, 'createAttributeMap']) || {};

        const attributes = {};

        Object.keys(attributeMap).forEach(attr => attributes[attributeMap[attr]] = this.model.get(attr));

        return attributes;
    }

    
    setup() {
        this.nameName = this.name + 'Name';
        this.idName = this.name + 'Id';

        this.foreignScope = this.options.foreignScope || this.foreignScope;

        this.foreignScope = this.foreignScope ||
            this.model.getFieldParam(this.name, 'entity') || this.model.getLinkParam(this.name, 'entity');

        if ('createDisabled' in this.options) {
            this.createDisabled = this.options.createDisabled;
        }

        if (!this.isListMode()) {
            this.addActionHandler('selectLink', () => this.actionSelect());
            this.addActionHandler('clearLink', () => this.clearLink());
        }

        if (this.isSearchMode()) {
            this.addActionHandler('selectLinkOneOf', () => this.actionSelectOneOf());

            this.events['click a[data-action="clearLinkOneOf"]'] = e =>{
                const id = $(e.currentTarget).data('id').toString();

                this.deleteLinkOneOf(id);
            };
        }

        this.autocompleteOnEmpty = this.params.autocompleteOnEmpty || this.autocompleteOnEmpty;

        this.createButton = this.params.createButton || this.createButton;

        if (this.createButton && !this.getAcl().checkScope(this.foreignScope, 'create')) {
            this.createButton = false;
        }

        if (this.createButton) {
            this.addActionHandler('createLink', () => this.actionCreateLink());
        }

        
        this.panelDefs = this.getMetadata()
            .get(['clientDefs', this.entityType, 'relationshipPanels', this.name]) || {};

        if (this.panelDefs.createDisabled) {
            this.createDisabled = true;
        }
    }

    
    select(model) {
        this.$elementName.val(model.get('name') || model.id);
        this.$elementId.val(model.get('id'));

        if (this.mode === this.MODE_SEARCH) {
            this.searchData.idValue = model.get('id');
            this.searchData.nameValue = model.get('name') || model.id;
        }

        this.trigger('change');

        this.controlCreateButtonVisibility();

        this.getSelectFieldHandler().then(handler => {
            handler.getAttributes(model)
                .then(attributes => {
                    this.model.set(attributes)
                });
        });
    }

    
    clearLink() {
        this.$elementName.val('');
        this.$elementId.val('');

        this.trigger('change');

        this.controlCreateButtonVisibility();

        this.getSelectFieldHandler().then(handler => {
            handler.getClearAttributes()
                .then(attributes => {
                    this.model.set(attributes);
                });
        });
    }

    
    controlCreateButtonVisibility() {
        if (!this.createButton) {
            return;
        }

        const $btn = this.$el.find('[data-action="createLink"]');

        this.model.get(this.idName) ?
            $btn.addClass('hidden') :
            $btn.removeClass('hidden');
    }

    
    getSelectFieldHandler() {
        if (!this.panelDefs.selectFieldHandler) {
            return Promise.resolve({
                getClearAttributes: () => Promise.resolve({}),
                getAttributes: () => Promise.resolve({}),
            });
        }

        return new Promise(resolve => {
            Espo.loader.requirePromise(this.panelDefs.selectFieldHandler)
                .then(Handler => {
                    const handler = new Handler(this.getHelper());

                    resolve(handler);
                });
        });
    }

    
    setupSearch() {
        this.searchData.oneOfIdList = this.getSearchParamsData().oneOfIdList ||
            this.searchParams.oneOfIdList || [];

        this.searchData.oneOfNameHash = this.getSearchParamsData().oneOfNameHash ||
            this.searchParams.oneOfNameHash || {};

        if (~['is', 'isNot', 'equals'].indexOf(this.getSearchType())) {
            this.searchData.idValue = this.getSearchParamsData().idValue ||
                this.searchParams.idValue || this.searchParams.value;

            this.searchData.nameValue = this.getSearchParamsData().nameValue ||
                this.searchParams.nameValue || this.searchParams.valueName;
        }

        this.events['change select.search-type'] = e => {
            const type = $(e.currentTarget).val();

            this.handleSearchType(type);
        };
    }

    
    handleSearchType(type) {
        if (~['is', 'isNot', 'isNotAndIsNotEmpty'].indexOf(type)) {
            this.$el.find('div.primary').removeClass('hidden');
        }
        else {
            this.$el.find('div.primary').addClass('hidden');
        }

        if (~['isOneOf', 'isNotOneOf', 'isNotOneOfAndIsNotEmpty'].indexOf(type)) {
            this.$el.find('div.one-of-container').removeClass('hidden');
        }
        else {
            this.$el.find('div.one-of-container').addClass('hidden');
        }
    }

    
    getAutocompleteMaxCount() {
        if (this.autocompleteMaxCount) {
            return this.autocompleteMaxCount;
        }

        return this.getConfig().get('recordsPerPage');
    }

    
    
    getAutocompleteUrl(q) {
        let url = this.foreignScope + '?maxSize=' + this.getAutocompleteMaxCount();

        if (!this.forceSelectAllAttributes) {
            const mandatorySelectAttributeList = this.mandatorySelectAttributeList ||
                this.panelDefs.selectMandatoryAttributeList;

            let select = ['id', 'name'];

            if (mandatorySelectAttributeList) {
                select = select.concat(mandatorySelectAttributeList);
            }

            url += '&select=' + select.join(',');
        }

        if (this.panelDefs.selectHandler) {
            return new Promise(resolve => {
                this._getSelectFilters().then(filters => {
                    if (filters.bool) {
                        url += '&' + $.param({'boolFilterList': filters.bool});
                    }

                    if (filters.primary) {
                        url += '&' + $.param({'primaryFilter': filters.primary});
                    }

                    if (filters.advanced && Object.keys(filters.advanced).length) {
                        url += '&' + $.param({'where': filters.advanced});
                    }

                    const orderBy = filters.orderBy || this.panelDefs.selectOrderBy;
                    const orderDirection = filters.orderBy ? filters.order : this.panelDefs.selectOrderDirection;

                    if (orderBy) {
                        url += '&' + $.param({
                            orderBy: orderBy,
                            order: orderDirection || 'asc',
                        });
                    }

                    resolve(url);
                });
            });
        }

        const boolList = [
            ...(this.getSelectBoolFilterList() || []),
            ...(this.panelDefs.selectBoolFilterList || []),
        ];

        const primary = this.getSelectPrimaryFilterName() || this.panelDefs.selectPrimaryFilterName;

        if (boolList.length) {
            url += '&' + $.param({'boolFilterList': boolList});
        }

        if (primary) {
            url += '&' + $.param({'primaryFilter': primary});
        }

        if (this.panelDefs.selectOrderBy) {
            const direction = this.panelDefs.selectOrderDirection || 'asc';

            url += '&' + $.param({
                orderBy: this.panelDefs.selectOrderBy,
                order: direction,
            });
        }

        return url;
    }

    
    afterRender() {
        if (this.isEditMode() || this.isSearchMode()) {
            this.$elementId = this.$el.find('input[data-name="' + this.idName + '"]');
            this.$elementName = this.$el.find('input[data-name="' + this.nameName + '"]');

            this.$elementName.on('change', () => {
                if (this.$elementName.val() === '') {
                    this.clearLink();
                }
            });

            this.$elementName.on('blur', e => {
                setTimeout(() => {
                    if (this.mode === this.MODE_EDIT && this.model.has(this.nameName)) {
                        e.currentTarget.value = this.model.get(this.nameName);
                    }
                }, 100);

                if (!this.autocompleteDisabled) {
                    setTimeout(() => this.$elementName.autocomplete('clear'), 300);
                }
            });

            const $elementName = this.$elementName;

            if (!this.autocompleteDisabled) {
                let isEmptyQueryResult = false;

                if (this.getEmptyAutocompleteResult()) {
                    this.$elementName.on('keydown', e => {
                        if (e.code === 'Tab' && isEmptyQueryResult) {
                            e.stopImmediatePropagation();
                        }
                    });
                }

                this.$elementName.autocomplete({
                    beforeRender: $c => {
                        if (this.$elementName.hasClass('input-sm')) {
                            $c.addClass('small');
                        }

                        
                        
                        if (this.$elementName.get(0) !== document.activeElement) {
                            setTimeout(() => this.$elementName.autocomplete('hide'), 30);
                        }
                    },
                    lookup: (q, callback) => {
                        if (!this.autocompleteOnEmpty && q.length === 0) {
                            isEmptyQueryResult = true;

                            const emptyResult = this.getEmptyAutocompleteResult();

                            if (emptyResult) {
                                callback(this._transformAutocompleteResult(emptyResult));
                            }

                            return;
                        }

                        isEmptyQueryResult = false;

                        Promise.resolve(this.getAutocompleteUrl(q))
                            .then(url => {
                                Espo.Ajax
                                    .getRequest(url, {q: q})
                                    .then(response => {
                                        callback(this._transformAutocompleteResult(response));
                                    });
                            });
                    },
                    minChars: 0,
                    triggerSelectOnValidInput: false,
                    autoSelectFirst: true,
                    noCache: true,
                    formatResult: suggestion => {
                        
                        return this.getHelper().escapeString(suggestion.name);
                    },
                    onSelect: s => {
                        this.getModelFactory().create(this.foreignScope, (model) => {
                            
                            model.set(s.attributes);

                            this.select(model);

                            this.$elementName.focus();
                        });
                    },
                });

                this.$elementName.off('focus.autocomplete');

                this.$elementName.on('focus', () => {
                    if (this.$elementName.val()) {
                        this.$elementName.get(0).select();

                        return;
                    }

                    this.$elementName.autocomplete('onFocus');
                });

                this.$elementName.attr('autocomplete', 'espo-' + this.name);

                this.once('render', () => {
                    $elementName.autocomplete('dispose');
                });

                this.once('remove', () => {
                    $elementName.autocomplete('dispose');
                });

                if (this.isSearchMode()) {
                    const $elementOneOf = this.$el.find('input.element-one-of');

                    
                    $elementOneOf.autocomplete({
                        beforeRender: $c => {
                            if (this.$elementName.hasClass('input-sm')) {
                                $c.addClass('small');
                            }
                        },
                        serviceUrl: () => {
                            return this.getAutocompleteUrl();
                        },
                        minChars: 1,
                        paramName: 'q',
                        noCache: true,
                        formatResult: suggestion => {
                            
                            return this.getHelper().escapeString(suggestion.name);
                        },
                        transformResult: response => {
                            return this._transformAutocompleteResult(JSON.parse(response));
                        },
                        onSelect: s => {
                            this.getModelFactory().create(this.foreignScope, model => {
                                
                                model.set(s.attributes);

                                this.selectOneOf([model]);

                                $elementOneOf.val('');
                                setTimeout(() => $elementOneOf.focus(), 50);
                            });
                        },
                    });

                    $elementOneOf.attr('autocomplete', 'espo-' + this.name);

                    this.once('render', () => {
                        $elementOneOf.autocomplete('dispose');
                    });

                    this.once('remove', () => {
                        $elementOneOf.autocomplete('dispose');
                    });

                    this.$el.find('select.search-type').on('change', () => {
                        this.trigger('change');
                    });
                }
            }

            $elementName.on('change', () => {
                if (!this.isSearchMode() && !this.model.get(this.idName)) {
                    $elementName.val(this.model.get(this.nameName));
                }
            });
        }

        if (this.isSearchMode()) {
            const type = this.$el.find('select.search-type').val();

            this.handleSearchType(type);

            if (~['isOneOf', 'isNotOneOf', 'isNotOneOfAndIsNotEmpty'].indexOf(type)) {
                this.searchData.oneOfIdList.forEach(id => {
                    this.addLinkOneOfHtml(id, this.searchData.oneOfNameHash[id]);
                });
            }
        }
    }

    
    _transformAutocompleteResult(response) {
        const list = [];

        response.list.forEach(item => {
            list.push({
                id: item.id,
                name: item.name || item.id,
                data: item.id,
                value: item.name || item.id,
                attributes: item,
            });
        });

        return {suggestions: list};
    }

    
    getValueForDisplay() {
        return this.model.get(this.nameName);
    }

    
    validateRequired() {
        if (this.isRequired()) {
            if (this.model.get(this.idName) == null) {
                const msg = this.translate('fieldIsRequired', 'messages')
                    .replace('{field}', this.getLabelText());

                this.showValidationMessage(msg);

                return true;
            }
        }
    }

    
    deleteLinkOneOf(id) {
        this.deleteLinkOneOfHtml(id);

        const index = this.searchData.oneOfIdList.indexOf(id);

        if (index > -1) {
            this.searchData.oneOfIdList.splice(index, 1);
        }

        delete this.searchData.oneOfNameHash[id];

        this.trigger('change');
    }

    
    addLinkOneOf(id, name) {
        if (!~this.searchData.oneOfIdList.indexOf(id)) {
            this.searchData.oneOfIdList.push(id);
            this.searchData.oneOfNameHash[id] = name;
            this.addLinkOneOfHtml(id, name);

            this.trigger('change');
        }
    }

    
    deleteLinkOneOfHtml(id) {
        this.$el.find('.link-one-of-container .link-' + id).remove();
    }

    
    addLinkOneOfHtml(id, name) {
        const $container = this.$el.find('.link-one-of-container');

        const $el = $('<div>')
            .addClass('link-' + id)
            .addClass('list-group-item');

        $el.append(
            $('<a>')
                .attr('role', 'button')
                .addClass('pull-right')
                .attr('data-id', id)
                .attr('data-action', 'clearLinkOneOf')
                .append(
                    $('<span>').addClass('fas fa-times')
                ),
            $('<span>').text(name),
            ' '
        );

        $container.append($el);

        return $el;
    }

    
    fetch() {
        const data = {};

        data[this.nameName] = this.$el.find('[data-name="'+this.nameName+'"]').val() || null;
        data[this.idName] = this.$el.find('[data-name="'+this.idName+'"]').val() || null;

        return data;
    }

    
    fetchSearch() {
        const type = this.$el.find('select.search-type').val();
        const value = this.$el.find('[data-name="' + this.idName + '"]').val();

        if (~['isOneOf', 'isNotOneOf'].indexOf(type) && !this.searchData.oneOfIdList.length) {
            return {
                type: 'isNotNull',
                attribute: 'id',
                data: {
                    type: type,
                },
            };
        }

        if (type === 'isEmpty') {
            return {
                type: 'isNull',
                attribute: this.idName,
                data: {
                    type: type,
                }
            };
        }

        if (type === 'isNotEmpty') {
            return {
                type: 'isNotNull',
                attribute: this.idName,
                data: {
                    type: type,
                },
            };
        }

        if (type === 'isOneOf') {
            return {
                type: 'in',
                attribute: this.idName,
                value: this.searchData.oneOfIdList,
                data: {
                    type: type,
                    oneOfIdList: this.searchData.oneOfIdList,
                    oneOfNameHash: this.searchData.oneOfNameHash,
                },
            };
        }

        if (type === 'isNotOneOf') {
            return {
                type: 'or',
                value: [
                    {
                        type: 'notIn',
                        attribute: this.idName,
                        value: this.searchData.oneOfIdList,
                    },
                    {
                        type: 'isNull',
                        attribute: this.idName,
                    },
                ],
                data: {
                    type: type,
                    oneOfIdList: this.searchData.oneOfIdList,
                    oneOfNameHash: this.searchData.oneOfNameHash,
                }
            };
        }

        if (type === 'isNotOneOfAndIsNotEmpty') {
            return {
                type: 'notIn',
                attribute: this.idName,
                value: this.searchData.oneOfIdList,
                data: {
                    type: type,
                    oneOfIdList: this.searchData.oneOfIdList,
                    oneOfNameHash: this.searchData.oneOfNameHash,
                },
            };
        }

        if (type === 'isNot') {
            if (!value) {
                return false;
            }

            const nameValue = this.$el.find('[data-name="' + this.nameName + '"]').val();

            return {
                type: 'or',
                value: [
                    {
                        type: 'notEquals',
                        attribute: this.idName,
                        value: value
                    },
                    {
                        type: 'isNull',
                        attribute: this.idName,
                    }
                ],
                data: {
                    type: type,
                    idValue: value,
                    nameValue: nameValue,
                }
            };
        }

        if (type === 'isNotAndIsNotEmpty') {
            if (!value) {
                return false;
            }

            const nameValue = this.$el.find('[data-name="' + this.nameName + '"]').val();

            return {
                type: 'notEquals',
                attribute: this.idName,
                value: value,
                data: {
                    type: type,
                    idValue: value,
                    nameValue: nameValue,
                },
            };
        }

        if (!value) {
            return false;
        }

        const nameValue = this.$el.find('[data-name="' + this.nameName + '"]').val();

        return {
            type: 'equals',
            attribute: this.idName,
            value: value,
            data: {
                type: type,
                idValue: value,
                nameValue: nameValue,
            }
        };
    }

    
    getSearchType() {
        return this.getSearchParamsData().type ||
            this.searchParams.typeFront ||
            this.searchParams.type;
    }

    
    quickView() {
        const id = this.model.get(this.idName);

        if (!id) {
            return;
        }

        const entityType = this.foreignScope;

        const helper = new RecordModal(this.getMetadata(), this.getAcl());

        helper.showDetail(this, {
            id: id,
            scope: entityType,
        });
    }

    
    getCreateAttributesProvider() {
        return () => {
            const attributes = this.getCreateAttributes() || {};

            if (!this.panelDefs.createHandler) {
                return Promise.resolve(attributes);
            }

            return new Promise(resolve => {
                Espo.loader.requirePromise(this.panelDefs.createHandler)
                    .then(Handler => new Handler(this.getHelper()))
                    .then(handler => {
                        handler.getAttributes(this.model)
                            .then(additionalAttributes => {
                                resolve({
                                    ...attributes,
                                    ...additionalAttributes,
                                });
                            });
                    });
            });
        };
    }

    
    actionSelect() {
        Espo.Ui.notify(' ... ');

        const panelDefs = this.panelDefs;

        const viewName = panelDefs.selectModalView ||
            this.getMetadata().get(['clientDefs', this.foreignScope, 'modalViews', 'select']) ||
            this.selectRecordsView;

        const mandatorySelectAttributeList = this.mandatorySelectAttributeList ||
            panelDefs.selectMandatoryAttributeList;

        const createButton = this.isEditMode() && (!this.createDisabled || this.forceCreateButton);

        const createAttributesProvider = createButton ?
            this.getCreateAttributesProvider() :
            null;

        this._getSelectFilters().then(filters => {
            const orderBy = filters.orderBy || this.panelDefs.selectOrderBy;
            const orderDirection = filters.orderBy ? filters.order : this.panelDefs.selectOrderDirection;

            this.createView('dialog', viewName, {
                scope: this.foreignScope,
                createButton: createButton,
                filters: filters.advanced,
                boolFilterList: filters.bool,
                primaryFilterName: filters.primary,
                mandatorySelectAttributeList: mandatorySelectAttributeList,
                forceSelectAllAttributes: this.forceSelectAllAttributes,
                filterList: this.getSelectFilterList(),
                createAttributesProvider: createAttributesProvider,
                layoutName: this.panelDefs.selectLayout,
                orderBy: orderBy,
                orderDirection: orderDirection,
            }, view => {
                view.render();

                Espo.Ui.notify(false);

                this.listenToOnce(view, 'select', model => {
                    this.clearView('dialog');

                    this.select(model);
                });
            });
        });
    }

    
    _applyAdditionalFilter(advanced) {
        const foreignLink = this.model.getLinkParam(this.name, 'foreign');

        if (!foreignLink) {
            return;
        }

        if (advanced[foreignLink]) {
            return;
        }

        const linkType = this.model.getLinkParam(this.name, 'type');
        const foreignLinkType = this.getMetadata()
            .get(['entityDefs', this.foreignScope, 'links', foreignLink, 'type']);
        const foreignFieldType = this.getMetadata()
            .get(['entityDefs', this.foreignScope, 'fields', foreignLink, 'type']);

        if (!foreignFieldType) {
            return;
        }

        const isOneToOne =
            (linkType === 'hasOne' || foreignLinkType === 'hasOne') &&
            ['link', 'linkOne'].includes(foreignFieldType);

        if (!isOneToOne) {
            return;
        }

        advanced[foreignLink] = {
            type: 'isNull',
            attribute: foreignLink + 'Id',
            data: {
                type: 'isEmpty',
            },
        };
    }

    
    _getSelectFilters() {
        const handler = this.panelDefs.selectHandler;

        const localBoolFilterList = this.getSelectBoolFilterList();

        if (!handler || this.isSearchMode()) {
            const boolFilterList = (localBoolFilterList || this.panelDefs.selectBoolFilterList) ?
                [
                    ...(localBoolFilterList || []),
                    ...(this.panelDefs.selectBoolFilterList || []),
                ] :
                undefined;

            const advanced = this.getSelectFilters() || {};

            this._applyAdditionalFilter(advanced);

            return Promise.resolve({
                primary: this.getSelectPrimaryFilterName() || this.panelDefs.selectPrimaryFilterName,
                bool: boolFilterList,
                advanced: advanced,
            });
        }

        return new Promise(resolve => {
            Espo.loader.requirePromise(handler)
                .then(Handler => new Handler(this.getHelper()))
                .then(handler => {
                    return handler.getFilters(this.model);
                })
                .then(filters => {
                    const advanced = {...(this.getSelectFilters() || {}), ...(filters.advanced || {})};
                    const primaryFilter = this.getSelectPrimaryFilterName() ||
                        filters.primary || this.panelDefs.selectPrimaryFilterName;

                    const boolFilterList = (localBoolFilterList || filters.bool || this.panelDefs.selectBoolFilterList) ?
                        [
                            ...(localBoolFilterList || []),
                            ...(filters.bool || []),
                            ...(this.panelDefs.selectBoolFilterList || []),
                        ] :
                        undefined;

                    this._applyAdditionalFilter(advanced);

                    const orderBy = filters.orderBy;
                    const order = orderBy ? filters.order : undefined;

                    resolve({
                        bool: boolFilterList,
                        primary: primaryFilter,
                        advanced: advanced,
                        orderBy: orderBy,
                        order: order,
                    });
                });
        });
    }

    actionSelectOneOf() {
        Espo.Ui.notify(' ... ');

        const viewName = this.getMetadata().get(['clientDefs', this.foreignScope, 'modalViews', 'select']) ||
            this.selectRecordsView;

        this.createView('dialog', viewName, {
            scope: this.foreignScope,
            createButton: false,
            filters: this.getSelectFilters(),
            boolFilterList: this.getSelectBoolFilterList(),
            primaryFilterName: this.getSelectPrimaryFilterName(),
            multiple: true,
            layoutName: this.panelDefs.selectLayout,
        }, view => {
            view.render();

            Espo.Ui.notify(false);

            this.listenToOnce(view, 'select', models => {
                this.clearView('dialog');

                if (Object.prototype.toString.call(models) !== '[object Array]') {
                    models = [models];
                }

                this.selectOneOf(models);
            });
        });
    }

    getEmptyAutocompleteResult() {
        return undefined;
    }

    actionCreateLink() {
        const viewName = this.getMetadata().get(['clientDefs', this.foreignScope, 'modalViews', 'edit']) ||
            'views/modals/edit';

        Espo.Ui.notify(' ... ');

        this.getCreateAttributesProvider()().then(attributes => {
            this.createView('dialog', viewName, {
                scope: this.foreignScope,
                fullFormDisabled: true,
                attributes: attributes,
            }, view => {
                view.render()
                    .then(() => Espo.Ui.notify(false));

                this.listenToOnce(view, 'after:save', model => {
                    view.close();
                    this.clearView('dialog');

                    this.select(model);
                });
            });
        });
    }

    
    selectOneOf(models) {
        models.forEach(model => {
            this.addLinkOneOf(model.id, model.get('name'));
        });
    }
}

export default LinkFieldView;
