



import BaseFieldView from 'views/fields/base';
import RecordModal from 'helpers/record-modal';
import Select from 'ui/select';


class LinkParentFieldView extends BaseFieldView {

    type = 'linkParent'

    listTemplate = 'fields/link-parent/list'
    detailTemplate = 'fields/link-parent/detail'
    editTemplate = 'fields/link-parent/edit'
    searchTemplate = 'fields/link-parent/search'
    listLinkTemplate = 'fields/link-parent/list-link'

    
    nameName = null

    
    idName = null

    
    typeName = null

    
    foreignScope = null

    
    foreignScopeList = null

    
    autocompleteDisabled = false

    
    selectRecordsView = 'views/modals/select-records'

    
    createDisabled = false

    
    searchTypeList = [
        'is',
        'isEmpty',
        'isNotEmpty',
    ]

    
    selectPrimaryFilterName = null

    
    selectBoolFilterList = null

    
    autocompleteMaxCount = null

    
    forceSelectAllAttributes = false

    
    mandatorySelectAttributeList = null

    
    initialSearchIsNotIdle = true

    
    events = {
        
        'auxclick a[href]:not([role="button"])': function (e) {
            if (!this.isReadMode()) {
                return;
            }

            let isCombination = e.button === 1 && (e.ctrlKey || e.metaKey);

            if (!isCombination) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.quickView();
        },
    }

    data() {
        let nameValue = this.model.get(this.nameName);

        if (!nameValue && this.model.get(this.idName) && this.model.get(this.typeName)) {
            nameValue = this.translate(this.model.get(this.typeName), 'scopeNames');
        }

        let iconHtml = null;

        if (
            (
                this.mode === this.MODE_DETAIL ||
                this.mode === this.MODE_LIST && this.displayScopeColorInListMode
            ) &&
            this.foreignScope
        ) {
            iconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);
        }

        return {
            ...super.data(),
            idName: this.idName,
            nameName: this.nameName,
            typeName: this.typeName,
            idValue: this.model.get(this.idName),
            nameValue: nameValue,
            typeValue: this.model.get(this.typeName),
            foreignScope: this.foreignScope,
            foreignScopeList: this.foreignScopeList,
            valueIsSet: this.model.has(this.idName) || this.model.has(this.typeName),
            iconHtml: iconHtml,
            displayEntityType: this.displayEntityType && this.model.get(this.typeName),
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

    
    getCreateAttributes() {
        return null;
    }

    
    setup() {
        this.nameName = this.name + 'Name';
        this.typeName = this.name + 'Type';
        this.idName = this.name + 'Id';

        this.foreignScopeList = this.options.foreignScopeList || this.foreignScopeList;

        this.foreignScopeList = this.foreignScopeList ||
            this.params.entityList ||
            this.model.getLinkParam(this.name, 'entityList') || [];

        this.foreignScopeList = Espo.Utils.clone(this.foreignScopeList).filter(item => {
            if (!this.getMetadata().get(['scopes', item, 'disabled'])) {
                return true;
            }
        });

        this.foreignScope = this.model.get(this.typeName) || this.foreignScopeList[0];

        if (this.foreignScope && !~this.foreignScopeList.indexOf(this.foreignScope)) {
            this.foreignScopeList.unshift(this.foreignScope);
        }

        this.listenTo(this.model, 'change:' + this.typeName, () => {
            this.foreignScope = this.model.get(this.typeName) || this.foreignScopeList[0];
        });

        if ('createDisabled' in this.options) {
            this.createDisabled = this.options.createDisabled;
        }

        if (!this.isListMode()) {
            this.addActionHandler('selectLink', () => {
                Espo.Ui.notify(' ... ');

                let viewName = this.getMetadata()
                        .get('clientDefs.' + this.foreignScope + '.modalViews.select') ||
                    this.selectRecordsView;

                let createButton = !this.createDisabled && this.isEditMode();

                this.createView('dialog', viewName, {
                    scope: this.foreignScope,
                    createButton: createButton,
                    filters: this.getSelectFilters(),
                    boolFilterList: this.getSelectBoolFilterList(),
                    primaryFilterName: this.getSelectPrimaryFilterName(),
                    createAttributes: createButton ? this.getCreateAttributes() : null,
                    mandatorySelectAttributeList: this.getMandatorySelectAttributeList(),
                    forceSelectAllAttributes: this.isForceSelectAllAttributes(),
                }, dialog => {
                    dialog.render();

                    Espo.Ui.notify(false);

                    this.listenToOnce(dialog, 'select', (model) => {
                        this.clearView('dialog');
                        this.select(model);
                    });
                });
            });

            this.addActionHandler('clearLink', () => {
                if (this.foreignScopeList.length) {
                    this.foreignScope = this.foreignScopeList[0];
                    Select.setValue(this.$elementType, this.foreignScope);
                }

                this.$elementName.val('');
                this.$elementId.val('');

                this.trigger('change');
            });

            this.events['change select[data-name="'+this.typeName+'"]'] = (e) => {
                this.foreignScope = e.currentTarget.value;
                this.$elementName.val('');
                this.$elementId.val('');
            };
        }
    }

    
    setupSearch() {
        let type = this.getSearchParamsData().type;

        if (type === 'is' || !type) {
            this.searchData.idValue = this.getSearchParamsData().idValue ||
                this.searchParams.valueId;
            this.searchData.nameValue = this.getSearchParamsData().nameValue ||
                this.searchParams.valueName;
            this.searchData.typeValue = this.getSearchParamsData().typeValue ||
                this.searchParams.valueType;
        }

        this.events['change select.search-type'] = e => {
            let type = $(e.currentTarget).val();

            this.handleSearchType(type);
        };
    }

    
    handleSearchType(type) {
        if (~['is'].indexOf(type)) {
            this.$el.find('div.primary').removeClass('hidden');
        } else {
            this.$el.find('div.primary').addClass('hidden');
        }
    }

    
    select(model) {
        this.$elementName.val(model.get('name') || model.id);
        this.$elementId.val(model.get('id'));

        this.trigger('change');
    }

    
    getMandatorySelectAttributeList() {
        return this.mandatorySelectAttributeList;
    }

    
    isForceSelectAllAttributes() {
        return this.forceSelectAllAttributes;
    }

    
    getAutocompleteMaxCount() {
        if (this.autocompleteMaxCount) {
            return this.autocompleteMaxCount;
        }

        return this.getConfig().get('recordsPerPage');
    }

    
    getAutocompleteUrl() {
        let url = this.foreignScope + '?maxSize=' + this.getAutocompleteMaxCount();

        if (!this.isForceSelectAllAttributes()) {
            let select = ['id', 'name'];

            if (this.getMandatorySelectAttributeList()) {
                select = select.concat(this.getMandatorySelectAttributeList());
            }

            url += '&select=' + select.join(',');
        }

        let boolList = this.getSelectBoolFilterList();

        if (boolList) {
            url += '&' + $.param({'boolFilterList': boolList});
        }

        let primary = this.getSelectPrimaryFilterName();

        if (primary) {
            url += '&' + $.param({'primaryFilter': primary});
        }

        return url;
    }

    afterRender() {
        if (this.isEditMode() || this.isSearchMode()) {
            this.$elementId = this.$el.find('input[data-name="' + this.idName + '"]');
            this.$elementName = this.$el.find('input[data-name="' + this.nameName + '"]');
            this.$elementType = this.$el.find('select[data-name="' + this.typeName + '"]');

            this.$elementName.on('change', () => {
                if (this.$elementName.val() === '') {
                    this.$elementName.val('');
                    this.$elementId.val('');

                    this.trigger('change');
                }
            });

            this.$elementType.on('change', () => {
                this.$elementName.val('');
                this.$elementId.val('');

                this.trigger('change');
            });

            this.$elementName.on('blur', e => {
                setTimeout(() => {
                    if (this.mode === this.MODE_EDIT) {
                        e.currentTarget.value = this.model.get(this.nameName) || '';
                    }
                }, 100);

                if (!this.autocompleteDisabled) {
                    setTimeout(() => this.$elementName.autocomplete('clear'), 300);
                }
            });

            if (!this.autocompleteDisabled) {
                this.$elementName.autocomplete({
                    serviceUrl: (q) => {
                        return this.getAutocompleteUrl(q);
                    },
                    minChars: 1,
                    paramName: 'q',
                    noCache: true,
                    triggerSelectOnValidInput: false,
                    autoSelectFirst: true,
                    beforeRender: ($c) => {
                        if (this.$elementName.hasClass('input-sm')) {
                            $c.addClass('small');
                        }
                    },
                    formatResult: (suggestion) => {
                        return this.getHelper().escapeString(suggestion.name);
                    },
                    transformResult: (response) => {
                        response = JSON.parse(response);
                        let list = [];

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
                    },
                    onSelect: (s) => {
                        this.getModelFactory().create(this.foreignScope, (model) => {
                            model.set(s.attributes);

                            this.select(model);
                            this.$elementName.focus();
                        });
                    },
                });

                this.$elementName.off('focus.autocomplete');
                this.$elementName.on('focus', () => this.$elementName.get(0).select());

                this.$elementName.attr('autocomplete', 'espo-' + this.name);

                Select.init(this.$elementType, {});
            }

            let $elementName = this.$elementName;

            this.once('render', () => {
                $elementName.autocomplete('dispose');
            });

            this.once('remove', () => {
                $elementName.autocomplete('dispose');
            });
        }

        if (this.mode === 'search') {
            let type = this.$el.find('select.search-type').val();

            this.handleSearchType(type);

            this.$el.find('select.search-type').on('change', () => {
                this.trigger('change');
            });
        }
    }

    
    getValueForDisplay() {
        return this.model.get(this.nameName);
    }

    
    validateRequired() {
        if (this.isRequired()) {
            if (this.model.get(this.idName) === null || !this.model.get(this.typeName)) {
                let msg = this.translate('fieldIsRequired', 'messages')
                    .replace('{field}', this.getLabelText());

                this.showValidationMessage(msg);

                return true;
            }
        }
    }

    
    fetch() {
        let data = {};

        data[this.typeName] = this.$elementType.val() || null;
        data[this.nameName] = this.$elementName.val() || null;
        data[this.idName] = this.$elementId.val() || null;

        if (data[this.idName] === null) {
            data[this.typeName] = null;
        }

        return data;
    }

    
    fetchSearch() {
        let type = this.$el.find('select.search-type').val();

        if (type === 'isEmpty') {
            return {
                type: 'isNull',
                field: this.idName,
                data: {
                    type: type,
                }
            };
        }

        if (type === 'isNotEmpty') {
            return {
                type: 'isNotNull',
                field: this.idName,
                data: {
                    type: type,
                }
            };
        }

        let entityType = this.$elementType.val();
        let entityName = this.$elementName.val()
        let entityId = this.$elementId.val();

        if (!entityType) {
            return null;
        }

        if (entityId) {
            return {
                type: 'and',
                attribute: this.idName,
                value: [
                    {
                        type: 'equals',
                        field: this.idName,
                        value: entityId,
                    },
                    {
                        type: 'equals',
                        field: this.typeName,
                        value: entityType,
                    }
                ],
                data: {
                    type: 'is',
                    idValue: entityId,
                    nameValue: entityName,
                    typeValue: entityType,
                }
            };
        }

        return {
            type: 'and',
            attribute: this.idName,
            value: [
                {
                    type: 'isNotNull',
                    field: this.idName,
                },
                {
                    type: 'equals',
                    field: this.typeName,
                    value: entityType,
                }
            ],
            data: {
                type: 'is',
                typeValue: entityType,
            }
        };
    }

    
    getSearchType() {
        return this.getSearchParamsData().type || this.searchParams.typeFront;
    }

    
    quickView() {
        let id = this.model.get(this.idName);
        let entityType = this.model.get(this.typeName);

        if (!id || !entityType) {
            return;
        }

        let helper = new RecordModal(this.getMetadata(), this.getAcl());

        helper.showDetail(this, {
            id: id,
            scope: entityType,
        });
    }
}

export default LinkParentFieldView;
