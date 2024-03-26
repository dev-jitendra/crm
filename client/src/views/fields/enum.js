



import BaseFieldView from 'views/fields/base';
import MultiSelect from 'ui/multi-select';
import Select from 'ui/select'


class EnumFieldView extends BaseFieldView {

    

    

    
    constructor(options) {
        super(options);
    }

    type = 'enum'

    listTemplate = 'fields/enum/list'
    listLinkTemplate = 'fields/enum/list-link'
    detailTemplate = 'fields/enum/detail'
    editTemplate = 'fields/enum/edit'
    searchTemplate = 'fields/enum/search'

    translatedOptions = null

    
    fetchEmptyValueAsNull = true

    searchTypeList = [
        'anyOf',
        'noneOf',
        'isEmpty',
        'isNotEmpty',
    ]

    validationElementSelector = '.selectize-control'

    
    data() {
        let data = super.data();

        data.translatedOptions = this.translatedOptions;

        let value = this.model.get(this.name);

        if (this.isReadMode() && this.styleMap) {
            data.style = this.styleMap[value || ''] || 'default';
        }

        if (this.isReadMode()) {
            if (this.params.displayAsLabel && data.style && data.style !== 'default') {
                data.class = 'label label-md label';
            } else {
                data.class = 'text';
            }
        }

        let translationKey = value || '';

        if (
            typeof value !== 'undefined' && value !== null && value !== ''
            ||
            translationKey === '' && (
                translationKey in (this.translatedOptions || {}) &&
                (this.translatedOptions || {})[translationKey] !== ''
            )
        ) {
            data.isNotEmpty = true;
        }

        data.valueIsSet = this.model.has(this.name);

        if (data.isNotEmpty) {
            data.valueTranslated =
                this.translatedOptions ?
                    (this.translatedOptions[translationKey] || value) :
                    this.getLanguage().translateOption(translationKey, this.name, this.entityType);

        }

        return data;
    }

    setup() {
        if (!this.params.options) {
            let methodName = 'get' + Espo.Utils.upperCaseFirst(this.name) + 'Options';

            if (typeof this.model[methodName] === 'function') {
                this.params.options = this.model[methodName].call(this.model);
            }
        }

        let optionsPath = this.params.optionsPath;
        
        let optionsReference = this.params.optionsReference;

        if (!optionsPath && optionsReference) {
            let [refEntityType, refField] = optionsReference.split('.');

            optionsPath = `entityDefs.${refEntityType}.fields.${refField}.options`;
        }

        if (optionsPath) {
            this.params.options = Espo.Utils.clone(this.getMetadata().get(optionsPath)) || [];
        }

        this.styleMap = this.params.style || this.model.getFieldParam(this.name, 'style') || {};

        this.setupOptions();

        if ('translatedOptions' in this.options) {
            this.translatedOptions = this.options.translatedOptions;
        }

        if ('translatedOptions' in this.params) {
            this.translatedOptions = this.params.translatedOptions;
        }

        this.setupTranslation();

        if (this.translatedOptions === null) {
            this.translatedOptions = this.getLanguage()
                .translate(this.name, 'options', this.model.name) || {};

            if (this.translatedOptions === this.name) {
                this.translatedOptions = null;
            }
        }

        if (this.params.isSorted && this.translatedOptions) {
            this.params.options = Espo.Utils.clone(this.params.options) || [];

            this.params.options = this.params.options.sort((v1, v2) => {
                 return (this.translatedOptions[v1] || v1)
                     .localeCompare(this.translatedOptions[v2] || v2);
            });
        }

        if (this.options.customOptionList) {
            this.setOptionList(this.options.customOptionList);
        }
    }

    setupTranslation() {
        let translation = this.params.translation;
        
        let optionsReference = this.params.optionsReference;

        if (!translation && optionsReference) {
            let [refEntityType, refField] = optionsReference.split('.');

            translation = `${refEntityType}.options.${refField}`;
        }

        if (!translation) {
            return;
        }

        this.translatedOptions = null;

        if (!this.params.options) {
            return;
        }

        let obj = this.getLanguage().translatePath(translation);

        let map = {};

        this.params.options.forEach(item => {
            if (typeof obj === 'object' && item in obj) {
                map[item] = obj[item];

                return;
            }

            if (
                Array.isArray(obj) &&
                typeof item === 'number' &&
                typeof obj[item] !== 'undefined'
            ) {
                map[item.toString()] = obj[item];

                return;
            }

            map[item] = item;
        });

        let value = this.model.get(this.name);

        if ((value || value === '') && !(value in map)) {
            if (typeof obj === 'object' && value in obj) {
                map[value] = obj[value];
            }
        }

        this.translatedOptions = map;
    }

    
    setupOptions() {}

    
    setOptionList(optionList) {
        let previousOptions = this.params.options;

        if (!this.originalOptionList) {
            this.originalOptionList = this.params.options;
        }

        let newOptions = Espo.Utils.clone(optionList) || [];

        this.params.options = newOptions;

        let isChanged = !_(previousOptions).isEqual(optionList);

        if (!this.isEditMode() || !isChanged) {
            return;
        }

        let triggerChange = false;
        let currentValue = this.model.get(this.name);

        if (!newOptions.includes(currentValue) && this.isReady) {
            this.model.set(this.name, newOptions[0] ?? null, {silent: true});

            triggerChange = true;
        }

        this.reRender()
            .then(() => {
                if (triggerChange) {
                    this.trigger('change');
                }
            });
    }

    
    resetOptionList() {
        if (!this.originalOptionList) {
            return;
        }

        let previousOptions = this.params.options;

        this.params.options = Espo.Utils.clone(this.originalOptionList);

        let isChanged = !_(previousOptions).isEqual(this.originalOptionList);

        if (!this.isEditMode() || !isChanged) {
            return;
        }

        if (this.isRendered()) {
            this.reRender();
        }
    }

    setupSearch() {
        this.events = _.extend({
            'change select.search-type': (e) => {
                this.handleSearchType($(e.currentTarget).val());
            },
        }, this.events || {});
    }

    handleSearchType(type) {
        var $inputContainer = this.$el.find('div.input-container');

        if (~['anyOf', 'noneOf'].indexOf(type)) {
            $inputContainer.removeClass('hidden');
        } else {
            $inputContainer.addClass('hidden');
        }
    }

    afterRender() {
        super.afterRender();

        if (this.isSearchMode()) {
            this.$element = this.$el.find('.main-element');

            let type = this.$el.find('select.search-type').val();

            this.handleSearchType(type);

            let valueList = this.getSearchParamsData().valueList || this.searchParams.value || [];

            this.$element.val(valueList.join(':,:'));

            let items = [];

            (this.params.options || []).forEach(value => {
                let label = this.getLanguage().translateOption(value, this.name, this.scope);

                if (this.translatedOptions) {
                    if (value in this.translatedOptions) {
                        label = this.translatedOptions[value];
                    }
                }

                if (label === '') {
                    return;
                }

                items.push({
                    value: value,
                    text: label,
                });
            });

            
            let multiSelectOptions = {
                items: items,
                delimiter: ':,:',
                matchAnyWord: true,
            };

            MultiSelect.init(this.$element, multiSelectOptions);

            this.$el.find('.selectize-dropdown-content').addClass('small');
            this.$el.find('select.search-type').on('change', () => this.trigger('change'));
            this.$element.on('change', () => this.trigger('change'));
        }

        if (this.isEditMode() || this.isSearchMode()) {
            Select.init(this.$element, {matchAnyWord: true});
        }
    }

    focusOnInlineEdit() {
        Select.focus(this.$element);
    }

    validateRequired() {
        if (this.isRequired()) {
            if (!this.model.get(this.name)) {
                let msg = this.translate('fieldIsRequired', 'messages')
                    .replace('{field}', this.getLabelText());

                this.showValidationMessage(msg);

                return true;
            }
        }
    }

    fetch() {
        let value = this.$element.val();

        if (this.fetchEmptyValueAsNull && !value) {
            value = null;
        }

        let data = {};

        data[this.name] = value;

        return data;
    }

    parseItemForSearch(item) {
        return item;
    }

    fetchSearch() {
        let type = this.fetchSearchType();

        let list = this.$element.val().split(':,:');

        if (list.length === 1 && list[0] === '') {
            list = [];
        }

        list.forEach((item, i) => {
            list[i] = this.parseItemForSearch(item);
        });

        if (type === 'anyOf') {
            if (list.length === 0) {
                return {
                    type: 'any',
                    data: {
                        type: 'anyOf',
                        valueList: list,
                    },
                };
            }

            return {
                type: 'in',
                value: list,
                data: {
                    type: 'anyOf',
                    valueList: list,
                },
            };
        }

        if (type === 'noneOf') {
            if (list.length === 0) {
                return {
                    type: 'any',
                    data: {
                        type: 'noneOf',
                        valueList: list,
                    },
                };
            }

            return {
                type: 'or',
                value: [
                    
                    {
                        type: 'notIn',
                        value: list,
                        attribute: this.name,
                    },
                    {
                        type: 'isNull',
                        attribute: this.name,
                    },
                ],
                data: {
                    type: 'noneOf',
                    valueList: list,
                },
            };
        }

        if (type === 'isEmpty') {
            return {
                type: 'or',
                value: [
                    {
                        type: 'isNull',
                        attribute: this.name,
                    },
                    {
                        type: 'equals',
                        value: '',
                        attribute: this.name,
                    }
                ],
                data: {
                    type: 'isEmpty',
                },
            };
        }

        if (type === 'isNotEmpty') {
            let value = [
                {
                    type: 'isNotNull',
                    attribute: this.name,
                },
            ];

            if (!this.model.getFieldParam(this.name, 'notStorable')) {
                value.push({
                    type: 'notEquals',
                    value: '',
                    attribute: this.name,
                });
            }

            return {
                type: 'and',
                value: value,
                data: {
                    type: 'isNotEmpty',
                },
            };
        }

        return null;
    }

    getSearchType() {
        return this.getSearchParamsData().type || 'anyOf';
    }
}

export default EnumFieldView;
