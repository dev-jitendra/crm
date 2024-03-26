



import BaseFieldView from 'views/fields/base';
import RecordModal from 'helpers/record-modal';


class LinkMultipleFieldView extends BaseFieldView {

    type = 'linkMultiple'

    listTemplate = 'fields/link-multiple/list'
    detailTemplate = 'fields/link-multiple/detail'
    editTemplate = 'fields/link-multiple/edit'
    searchTemplate = 'fields/link-multiple/search'

    
    nameHashName

    
    idsName

    
    nameHash = null

    
    ids = null

    
    foreignScope

    
    autocompleteDisabled = false

    
    selectRecordsView = 'views/modals/select-records'

    
    createDisabled = false

    
    forceCreateButton = false

    
    createButton = false

    
    sortable = false

    
    searchTypeList = [
        'anyOf',
        'isEmpty',
        'isNotEmpty',
        'noneOf',
        'allOf',
    ]

    
    selectFilterList = null

    
    selectBoolFilterList = null

    
    selectPrimaryFilterName = null

    
    autocompleteMaxCount = null

    
    autocompleteOnEmpty = false

    
    forceSelectAllAttributes = false

    
    iconHtml = ''

    
    events = {
        
        'auxclick a[href]:not([role="button"])': function (e) {
            if (!this.isReadMode()) {
                return;
            }

            const isCombination = e.button === 1 && (e.ctrlKey || e.metaKey);

            if (!isCombination) {
                return;
            }

            const id = $(e.currentTarget).attr('data-id');

            if (!id) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.quickView(id);
        },
    }

    
    
    data() {
        const ids = this.model.get(this.idsName);
        const createButton = this.createButton && (!this.createDisabled || this.forceCreateButton);

        
        return {
            ...super.data(),
            idValues: this.model.get(this.idsName),
            idValuesString: ids ? ids.join(',') : '',
            nameHash: this.model.get(this.nameHashName),
            foreignScope: this.foreignScope,
            valueIsSet: this.model.has(this.idsName),
            createButton: createButton,
        };
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
        this.nameHashName = this.name + 'Names';
        this.idsName = this.name + 'Ids';

        this.foreignScope = this.options.foreignScope ||
            this.foreignScope ||
            this.model.getFieldParam(this.name, 'entity') ||
            this.model.getLinkParam(this.name, 'entity');

        if ('createDisabled' in this.options) {
            this.createDisabled = this.options.createDisabled;
        }

        if (this.isSearchMode()) {
            const nameHash = this.getSearchParamsData().nameHash || this.searchParams.nameHash || {};
            const idList = this.getSearchParamsData().idList || this.searchParams.value || [];

            this.nameHash = Espo.Utils.clone(nameHash);
            this.ids = Espo.Utils.clone(idList);
        }
        else {
            this.copyValuesFromModel();
        }

        this.listenTo(this.model, 'change:' + this.idsName, () => {
            this.copyValuesFromModel();
        });

        this.sortable = this.sortable || this.params.sortable;

        this.iconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);

        if (!this.isListMode()) {
            this.addActionHandler('selectLink', () => this.actionSelect());

            this.events['click a[data-action="clearLink"]'] = (e) => {
                const id = $(e.currentTarget).attr('data-id');

                this.deleteLink(id);

                
                this.$element.get(0).focus({preventScroll: true});
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
    }

    
    copyValuesFromModel() {
        this.ids = Espo.Utils.clone(this.model.get(this.idsName) || []);
        this.nameHash = Espo.Utils.clone(this.model.get(this.nameHashName) || {});
    }

    
    handleSearchType(type) {
        if (~['anyOf', 'noneOf', 'allOf'].indexOf(type)) {
            this.$el.find('div.link-group-container').removeClass('hidden');
        }
        else {
            this.$el.find('div.link-group-container').addClass('hidden');
        }
    }

    
    setupSearch() {
        this.events = _.extend({
            'change select.search-type': (e) => {
                const type = $(e.currentTarget).val();

                this.handleSearchType(type);
            },
        }, this.events || {});
    }

    
    getAutocompleteMaxCount() {
        if (this.autocompleteMaxCount) {
            return this.autocompleteMaxCount;
        }

        return this.getConfig().get('recordsPerPage');
    }

    
    
    getAutocompleteUrl(q) {
        let url = this.foreignScope + '?&maxSize=' + this.getAutocompleteMaxCount();

        if (!this.forceSelectAllAttributes) {
            
            const panelDefs = this.getMetadata()
                .get(['clientDefs', this.entityType, 'relationshipPanels', this.name]) || {};

            const mandatorySelectAttributeList = this.mandatorySelectAttributeList ||
                panelDefs.selectMandatoryAttributeList;

            let select = ['id', 'name'];

            if (mandatorySelectAttributeList) {
                select = select.concat(mandatorySelectAttributeList);
            }

            url += '&select=' + select.join(',')
        }

        const notSelectedFilter = this.ids && this.ids.length ?
            {
                id: {
                    type: 'notIn',
                    attribute: 'id',
                    value: this.ids,
                }
            } :
            {};

        if (this.panelDefs.selectHandler) {
            return new Promise(resolve => {
                this._getSelectFilters().then(filters => {
                    if (filters.bool) {
                        url += '&' + $.param({boolFilterList: filters.bool});
                    }

                    if (filters.primary) {
                        url += '&' + $.param({primaryFilter: filters.primary});
                    }

                    const advanced = {
                        ...notSelectedFilter,
                        ...(filters.advanced || {}),
                    };

                    if (Object.keys(advanced).length) {
                        url += '&' + $.param({where: advanced});
                    }

                    resolve(url);
                });
            });
        }

        const boolList = [
            ...(this.getSelectBoolFilterList() || []),
            ...(this.panelDefs.selectBoolFilterList || []),
        ];

        if (boolList.length) {
            url += '&' + $.param({'boolFilterList': boolList});
        }

        const primary = this.getSelectPrimaryFilterName() || this.panelDefs.selectPrimaryFilterName;

        if (primary) {
            url += '&' + $.param({'primaryFilter': primary});
        }

        if (Object.keys(notSelectedFilter).length) {
            url += '&' + $.param({'where': notSelectedFilter});
        }

        return url;
    }

    
    afterRender() {
        if (this.isEditMode() || this.isSearchMode()) {
            this.$element = this.$el.find('input.main-element');

            const $element = this.$element;

            if (!this.autocompleteDisabled) {
                
                

                const minChar = this.autocompleteOnEmpty ? 0 : 1;

                this.$element.autocomplete({
                    lookup: (q, callback) => {
                        Promise.resolve(this.getAutocompleteUrl(q))
                            .then(url => {
                                Espo.Ajax
                                    .getRequest(url, {q: q})
                                    .then(response => {
                                        callback(this._transformAutocompleteResult(response));
                                    });
                            });
                    },
                    minChars: minChar,
                    paramName: 'q',
                    noCache: true,
                    autoSelectFirst: true,
                    triggerSelectOnValidInput: false,
                    beforeRender: $c => {
                        if (this.$element.hasClass('input-sm')) {
                            $c.addClass('small');
                        }

                        
                        
                        if (this.$element.get(0) !== document.activeElement) {
                            setTimeout(() => this.$element.autocomplete('hide'), 30);
                        }
                    },
                    formatResult: suggestion => {
                        
                        return this.getHelper().escapeString(suggestion.name);
                    },
                    transformResult: response => {
                        response = JSON.parse(response);

                        const list = [];

                        response.list.forEach((item) => {
                            list.push({
                                id: item.id,
                                name: item.name || item.id,
                                data: item.id,
                                value: item.name || item.id,
                            });
                        });

                        return {
                            suggestions: list
                        };
                    },
                    onSelect: s => {
                        this.getModelFactory().create(this.foreignScope, model => {
                            
                            model.set(s.attributes);

                            this.select([model])

                            this.$element.val('');
                            this.$element.focus();
                        });
                    },
                });

                this.$element.attr('autocomplete', 'espo-' + this.name);

                this.once('render', () => {
                    $element.autocomplete('dispose');
                });

                this.once('remove', () => {
                    $element.autocomplete('dispose');
                });
            }

            $element.on('change', () => {
                $element.val('');
            });

            this.renderLinks();

            if (this.isEditMode()) {
                if (this.sortable) {
                    
                    this.$el.find('.link-container').sortable({
                        stop: () => {
                            this.fetchFromDom();
                            this.trigger('change');
                        },
                    });
                }
            }

            if (this.isSearchMode()) {
                const type = this.$el.find('select.search-type').val();

                this.handleSearchType(type);

                this.$el.find('select.search-type').on('change', () => {
                    this.trigger('change');
                });
            }
        }
    }

    
    renderLinks() {
        this.ids.forEach(id => {
            this.addLinkHtml(id, this.nameHash[id]);
        });
    }

    
    deleteLink(id) {
        this.trigger('delete-link', id);
        this.trigger('delete-link:' + id);

        this.deleteLinkHtml(id);

        const index = this.ids.indexOf(id);

        if (index > -1) {
            this.ids.splice(index, 1);
        }

        delete this.nameHash[id];

        this.afterDeleteLink(id);
        this.trigger('change');
    }

    
    addLink(id, name) {
        if (!~this.ids.indexOf(id)) {
            this.ids.push(id);

            this.nameHash[id] = name;

            this.addLinkHtml(id, name);
            this.afterAddLink(id);

            this.trigger('add-link', id);
            this.trigger('add-link:' + id);
        }

        this.trigger('change');
    }

    
    afterDeleteLink(id) {}

    
    afterAddLink(id) {}

    
    deleteLinkHtml(id) {
        this.$el.find('.link-' + id).remove();
    }

    
    addLinkHtml(id, name) {
        

        name = name || id;

        const $container = this.$el.find('.link-container');

        const $el = $('<div>')
            .addClass('link-' + id)
            .addClass('list-group-item')
            .attr('data-id', id);

        $el.text(name).append('&nbsp;');

        $el.prepend(
            $('<a>')
                .addClass('pull-right')
                .attr('role', 'button')
                .attr('tabindex', '0')
                .attr('data-id', id)
                .attr('data-action', 'clearLink')
                .append(
                    $('<span>').addClass('fas fa-times')
                )
        );

        $container.append($el);

        return $el;
    }

    
    
    getIconHtml(id) {
        return this.iconHtml;
    }

    
    getDetailLinkHtml(id, name) {
        

        name = name || this.nameHash[id] || id;

        if (!name && id) {
            name = this.translate(this.foreignScope, 'scopeNames');
        }

        const iconHtml = this.isDetailMode() ?
            this.getIconHtml(id) : '';

        const $a = $('<a>')
            .attr('href', this.getUrl(id))
            .attr('data-id', id)
            .text(name);

        if (iconHtml) {
            $a.prepend(iconHtml)
        }

        return $a.get(0).outerHTML;
    }

    
    getUrl(id) {
        return '#' + this.foreignScope + '/view/' + id;
    }

    
    getValueForDisplay() {
        if (!this.isDetailMode() && !this.isListMode()) {
            return null;
        }

        const itemList = [];

        this.ids.forEach(id => {
            itemList.push(this.getDetailLinkHtml(id));
        });

        if (!itemList.length) {
            return null;
        }

        return itemList
            .map(item => $('<div>')
                .addClass('link-multiple-item')
                .html(item)
                .wrap('<div />').parent().html()
            )
            .join('');
    }

    
    validateRequired() {
        if (!this.isRequired()) {
            return false;
        }

        const idList = this.model.get(this.idsName) || [];

        if (idList.length === 0) {
            const msg = this.translate('fieldIsRequired', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }

        return false;
    }

    
    fetch() {
        const data = {};

        data[this.idsName] = Espo.Utils.clone(this.ids);
        data[this.nameHashName] = Espo.Utils.clone(this.nameHash);

        return data;
    }

    
    fetchFromDom() {
        this.ids = [];

        this.$el.find('.link-container').children().each((i, li) => {
            const id = $(li).attr('data-id');

            if (!id) {
                return;
            }

            this.ids.push(id);
        });
    }

    
    fetchSearch() {
        const type = this.$el.find('select.search-type').val();
        const idList = this.ids || [];

        if (~['anyOf', 'allOf', 'noneOf'].indexOf(type) && !idList.length) {
            return {
                type: 'isNotNull',
                attribute: 'id',
                data: {
                    type: type,
                },
            };
        }

        let data;

        if (type === 'anyOf') {
            data = {
                type: 'linkedWith',
                value: idList,
                data: {
                    type: type,
                    nameHash: this.nameHash,
                },
            };

            return data;
        }

        if (type === 'allOf') {
            data = {
                type: 'linkedWithAll',
                value: idList,
                data: {
                    type: type,
                    nameHash: this.nameHash,
                },
            };

            if (!idList.length) {
                data.value = null;
            }

            return data;
        }

        if (type === 'noneOf') {
            data = {
                type: 'notLinkedWith',
                value: idList,
                data: {
                    type: type,
                    nameHash: this.nameHash,
                },
            };

            return data;
        }

        if (type === 'isEmpty') {
            data = {
                type: 'isNotLinked',
                data: {
                    type: type,
                },
            };

            return data;
        }

        if (type === 'isNotEmpty') {
            data = {
                type: 'isLinked',
                data: {
                    type: type,
                },
            };

            return data;
        }
    }

    
    getSearchType() {
        return this.getSearchParamsData().type ||
            this.searchParams.typeFront ||
            this.searchParams.type || 'anyOf';
    }

    
    quickView(id) {
        const entityType = this.foreignScope;

        const helper = new RecordModal(this.getMetadata(), this.getAcl());

        helper.showDetail(this, {
            id: id,
            scope: entityType,
        });
    }

    
    actionSelect() {
        Espo.Ui.notify(' ... ');

        const panelDefs = this.panelDefs;

        const viewName = panelDefs.selectModalView ||
            this.getMetadata().get(`clientDefs.${this.foreignScope}.modalViews.select`) ||
            this.selectRecordsView;

        const mandatorySelectAttributeList = this.mandatorySelectAttributeList ||
            panelDefs.selectMandatoryAttributeList;

        const createButton = this.isEditMode() &&
            (!this.createDisabled && !panelDefs.createDisabled || this.forceCreateButton);

        const createAttributesProvider = createButton ?
            this.getCreateAttributesProvider() :
            null;

        this._getSelectFilters().then(filters => {
            this.createView('dialog', viewName, {
                scope: this.foreignScope,
                createButton: createButton,
                filters: filters.advanced,
                boolFilterList: filters.bool,
                primaryFilterName: filters.primary,
                filterList: this.getSelectFilterList(),
                multiple: true,
                mandatorySelectAttributeList: mandatorySelectAttributeList,
                forceSelectAllAttributes: this.forceSelectAllAttributes,
                createAttributesProvider: createAttributesProvider,
                layoutName: this.panelDefs.selectLayout,
            }, dialog => {
                dialog.render();

                Espo.Ui.notify(false);

                this.listenToOnce(dialog, 'select', models => {
                    this.clearView('dialog');

                    if (Object.prototype.toString.call(models) !== '[object Array]') {
                        models = [models];
                    }

                    this.select(models);
                });
            });
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

    
    select(models) {
        models.forEach(model => {
            this.addLink(model.id, model.get('name'));
        });
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

            return Promise.resolve({
                primary: this.getSelectPrimaryFilterName() || this.panelDefs.selectPrimaryFilterName,
                bool: boolFilterList,
                advanced: this.getSelectFilters() || undefined,
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

                    resolve({
                        bool: boolFilterList,
                        primary: primaryFilter,
                        advanced: advanced,
                    });
                });
        });
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

                    this.select([model]);
                });
            });
        });
    }
}

export default LinkMultipleFieldView;
