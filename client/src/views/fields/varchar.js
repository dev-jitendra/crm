



import BaseFieldView from 'views/fields/base';
import RegExpPattern from 'helpers/reg-exp-pattern';


class VarcharFieldView extends BaseFieldView {

    

    

    
    constructor(options) {
        super(options);
    }

    type = 'varchar'

    listTemplate = 'fields/varchar/list'
    detailTemplate = 'fields/varchar/detail'
    searchTemplate = 'fields/varchar/search'

    searchTypeList = [
        'startsWith',
        'contains',
        'equals',
        'endsWith',
        'like',
        'notContains',
        'notEquals',
        'notLike',
        'isEmpty',
        'isNotEmpty',
    ]

    
    validations = [
        'required',
        'pattern',
    ]

    
    useAutocompleteUrl = false

    
    noSpellCheck = false

    setup() {
        this.setupOptions();

        this.noSpellCheck = this.noSpellCheck || this.params.noSpellCheck;

        if (this.params.optionsPath) {
            this.params.options = Espo.Utils.clone(
                this.getMetadata().get(this.params.optionsPath) || []);
        }

        if (this.options.customOptionList) {
            this.setOptionList(this.options.customOptionList);
        }

        if (this.mode === this.MODE_DETAIL) {
            if (this.params.copyToClipboard) {
                this.events['click [data-action="copyToClipboard"]'] = () => this.copyToClipboard();
            }
        }
    }

    
    setupOptions() {}

    
    setOptionList(optionList) {
        if (!this.originalOptionList) {
            this.originalOptionList = this.params.options || [];
        }

        this.params.options = Espo.Utils.clone(optionList);

        if (this.isEditMode()) {
            if (this.isRendered()) {
                this.reRender();
            }
        }
    }

    
    resetOptionList() {
        if (this.originalOptionList) {
            this.params.options = Espo.Utils.clone(this.originalOptionList);
        }

        if (this.isEditMode()) {
            if (this.isRendered()) {
                this.reRender();
            }
        }
    }

    
    copyToClipboard() {
        const value = this.model.get(this.name);

        navigator.clipboard.writeText(value).then(() => {
            Espo.Ui.success(this.translate('Copied to clipboard'));
        });
    }

    
    
    getAutocompleteUrl(q) {
        return '';
    }

    transformAutocompleteResult(response) {
        const responseParsed = typeof response === 'string' ?
            JSON.parse(response) :
            response;

        const list = [];

        responseParsed.list.forEach(item => {
            list.push({
                id: item.id,
                name: item.name || item.id,
                data: item.id,
                value: item.name || item.id,
                attributes: item,
            });
        });

        return {
            suggestions: list,
        };
    }

    setupSearch() {
        this.events['change select.search-type'] = e => {
            const type = $(e.currentTarget).val();

            this.handleSearchType(type);
        };
    }

    data() {
        const data = super.data();

        if (
            this.model.get(this.name) !== null &&
            this.model.get(this.name) !== '' &&
            this.model.has(this.name)
        ) {
            data.isNotEmpty = true;
        }

        data.valueIsSet = this.model.has(this.name);

        if (this.isSearchMode()) {
            if (typeof this.searchParams.value === 'string') {
                this.searchData.value = this.searchParams.value;
            }

            if (this.searchParams.data && typeof this.searchParams.data.value === 'string') {
                this.searchData.value = this.searchParams.data.value;
            }

            if (!this.searchParams.value && !this.searchParams.data) {
                this.searchData.value = null;
            }
        }

        data.noSpellCheck = this.noSpellCheck;
        data.copyToClipboard = this.params.copyToClipboard;

        return data;
    }

    handleSearchType(type) {
        if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
            this.$el.find('input.main-element').addClass('hidden');

            return;
        }

        this.$el.find('input.main-element').removeClass('hidden');
    }

    afterRender() {
        super.afterRender();

        if (this.isSearchMode()) {
            const type = this.$el.find('select.search-type').val();

            this.handleSearchType(type);
        }

        if (
            (this.isEditMode() || this.isSearchMode()) &&
            (
                this.params.options && this.params.options.length ||
                this.useAutocompleteUrl
            )
        ) {
            
            const autocompleteOptions = {
                minChars: 0,
                lookup: this.params.options,
                maxHeight: 200,
                triggerSelectOnValidInput: false,
                autoSelectFirst: true,
                beforeRender: $c => {
                    if (this.$element.hasClass('input-sm')) {
                        $c.addClass('small');
                    }
                },
                formatResult: suggestion => {
                    return this.getHelper().escapeString(suggestion.value);
                },
                lookupFilter: (suggestion, query, queryLowerCase) => {
                    if (suggestion.value.toLowerCase().indexOf(queryLowerCase) === 0) {
                        return suggestion.value.length !== queryLowerCase.length;
                    }

                    return false;
                },
                onSelect: () => {
                    this.trigger('change');

                    this.$element.focus();
                },
            };

            if (this.useAutocompleteUrl) {
                autocompleteOptions.noCache = true;
                autocompleteOptions.lookup = (query, done) => {
                    Espo.Ajax.getRequest(this.getAutocompleteUrl(query))
                        .then(response => {
                            return this.transformAutocompleteResult(response);
                        })
                        .then(result => {
                            done(result);
                        });
                };
            }

            this.$element.autocomplete(autocompleteOptions);
            this.$element.attr('autocomplete', 'espo-' + this.name);

            
            this.$element.off('focus.autocomplete');

            this.$element.on('focus', () => {
                if (this.$element.val()) {
                    return;
                }

                this.$element.autocomplete('onValueChange');
            });

            this.once('render', () => this.$element.autocomplete('dispose'));
            this.once('remove', () => this.$element.autocomplete('dispose'));
        }

        if (this.isSearchMode()) {
            this.$el.find('select.search-type').on('change', () => {
                this.trigger('change');
            });

            this.$element.on('input', () => {
                this.trigger('change');
            });
        }
    }

    
    validatePattern() {
        const pattern = this.params.pattern;

        return this.fieldValidatePattern(this.name, pattern);
    }

    
    fieldValidatePattern(name, pattern) {
        pattern = pattern || this.model.getFieldParam(name, 'pattern');
        
        const value = this.model.get(name);

        if (!pattern) {
            return false;
        }

        const helper = new RegExpPattern(this.getMetadata(), this.getLanguage());
        const result = helper.validate(pattern, value, name, this.entityType);

        if (!result) {
            return false;
        }

        const message = result.message.replace('{field}', this.getLanguage().translate(this.getLabelText()));

        this.showValidationMessage(message, '[data-name="' + name + '"]');

        return true;
    }

    
    fetch() {
        const data = {};

        const value = this.$element.val().trim();

        data[this.name] = value || null;

        return data;
    }

    
    fetchSearch() {
        const type = this.fetchSearchType() || 'startsWith';

        if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
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
                            attribute: this.name,
                            value: '',
                        },
                    ],
                    data: {
                        type: type,
                    },
                };
            }

            const value = [
                {
                    type: 'isNotNull',
                    attribute: this.name,
                    value: null,
                },
            ];

            if (!this.model.getFieldParam(this.name, 'notStorable')) {
                value.push({
                    type: 'notEquals',
                    attribute: this.name,
                    value: '',
                });
            }

            return {
                type: 'and',
                value: value,
                data: {
                    type: type,
                },
            };
        }

        const value = this.$element.val().toString().trim();

        if (!value) {
            return null;
        }

        return {
            value: value,
            type: type,
            data: {
                type: type,
            },
        };
    }

    getSearchType() {
        return this.getSearchParamsData().type || this.searchParams.typeFront ||
            this.searchParams.type;
    }
}

export default VarcharFieldView;
