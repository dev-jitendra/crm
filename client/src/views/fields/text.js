



import BaseFieldView from 'views/fields/base';


class TextFieldView extends BaseFieldView {

    type = 'text'

    listTemplate = 'fields/text/list'
    detailTemplate = 'fields/text/detail'
    editTemplate = 'fields/text/edit'
    searchTemplate = 'fields/text/search'

    seeMoreText = false
    rowsDefault = 50000
    rowsMin = 2
    seeMoreDisabled = false
    cutHeight = 200
    noResize = false
    changeInterval = 5
    shrinkThreshold = 10;

    searchTypeList = [
        'contains',
        'startsWith',
        'equals',
        'endsWith',
        'like',
        'notContains',
        'notLike',
        'isEmpty',
        'isNotEmpty',
    ]

    events = {
        
        'click a[data-action="seeMoreText"]': function () {
            this.seeMoreText = true;

            this.reRender();
        },
        
        'click [data-action="mailTo"]': function (e) {
            this.mailTo($(e.currentTarget).data('email-address'));
        },
    }

    
    _lastLength

    
    maxRows

    setup() {
        super.setup();

        this.maxRows = this.params.rows || this.rowsDefault;
        this.noResize = this.options.noResize || this.params.noResize || this.noResize;
        this.seeMoreDisabled = this.seeMoreDisabled || this.params.seeMoreDisabled;
        this.autoHeightDisabled = this.options.autoHeightDisabled || this.params.autoHeightDisabled ||
            this.autoHeightDisabled;

        if (this.params.cutHeight) {
            this.cutHeight = this.params.cutHeight;
        }

        this.rowsMin = this.options.rowsMin || this.params.rowsMin || this.rowsMin;

        if (this.maxRows < this.rowsMin) {
            this.rowsMin = this.maxRows;
        }

        this.on('remove', () => {
            $(window).off('resize.see-more-' + this.cid);
        });
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

        if (this.mode === this.MODE_SEARCH) {
            if (typeof this.searchParams.value === 'string') {
                this.searchData.value = this.searchParams.value;
            }
        }

        if (this.mode === this.MODE_EDIT) {
            data.rows = this.autoHeightDisabled ?
                this.maxRows :
                this.rowsMin;
        }

        data.valueIsSet = this.model.has(this.name);

        if (this.isReadMode()) {
            data.isCut = this.isCut();

            if (data.isCut) {
                data.cutHeight = this.cutHeight;
            }

            data.displayRawText = this.params.displayRawText;
        }

        data.noResize = this.noResize || (!this.autoHeightDisabled && !this.params.rows);

        
        return data;
    }

    handleSearchType(type) {
        if (~['isEmpty', 'isNotEmpty'].indexOf(type)) {
            this.$el.find('input.main-element').addClass('hidden');
        } else {
            this.$el.find('input.main-element').removeClass('hidden');
        }
    }

    getValueForDisplay() {
        const text = this.model.get(this.name);

        return text || '';
    }

    
    controlTextareaHeight(lastHeight) {
        const scrollHeight = this.$element.prop('scrollHeight');
        const clientHeight = this.$element.prop('clientHeight');

        if (typeof lastHeight === 'undefined' && clientHeight === 0) {
            setTimeout(this.controlTextareaHeight.bind(this), 10);

            return;
        }

        
        const element = this.$element.get(0);

        if (!element || element.value === undefined) {
            return;
        }

        const length = element.value.length;

        if (this._lastLength === undefined) {
            this._lastLength = length;
        }

        if (length > this._lastLength) {
            this._lastLength = length;
        }

        if (clientHeight === lastHeight) {
            
            return;
        }

        if (scrollHeight > clientHeight + 1) {
            const rows = element.rows;

            if (this.maxRows && rows >= this.maxRows) {
                return;
            }

            element.rows ++;

            this.controlTextareaHeight(clientHeight);

            return;
        }

        if (this.$element.val().length === 0) {
            element.rows = this.rowsMin;

            return;
        }

        const tryShrink = () => {
            const rows = element.rows;

            if (this.rowsMin && rows - 1 <= this.rowsMin) {
                return;
            }

            element.rows --;

            if (element.scrollHeight > element.clientHeight + 1) {
                this.controlTextareaHeight();

                return;
            }

            tryShrink();
        };

        if (length < this._lastLength - this.shrinkThreshold) {
            this._lastLength = length;

            tryShrink();
        }
    }

    isCut() {
        return !this.seeMoreText && !this.seeMoreDisabled;
    }

    controlSeeMore() {
        if (!this.isCut()) {
            return;
        }

        if (this.$text.height() > this.cutHeight) {
            this.$seeMoreContainer.removeClass('hidden');
            this.$textContainer.addClass('cut');
        } else {
            this.$seeMoreContainer.addClass('hidden');
            this.$textContainer.removeClass('cut');
        }
    }

    afterRender() {
        super.afterRender();

        if (this.isReadMode()) {
            $(window).off('resize.see-more-' + this.cid);

            this.$textContainer = this.$el.find('> .complex-text-container');
            this.$text = this.$textContainer.find('> .complex-text');
            this.$seeMoreContainer = this.$el.find('> .see-more-container');

            if (this.isCut()) {
                this.controlSeeMore();

                if (this.model.get(this.name) && this.$text.height() === 0) {
                    this.$textContainer.addClass('cut');

                    setTimeout(this.controlSeeMore.bind(this), 50);
                }

                this.listenTo(this.recordHelper, 'panel-show', () => this.controlSeeMore());
                this.on('panel-show-propagated', () => this.controlSeeMore());

                $(window).on('resize.see-more-' + this.cid, () => {
                    this.controlSeeMore();
                });
            }
        }

        if (this.mode === this.MODE_EDIT) {
            const text = this.getValueForDisplay();

            if (text) {
                this.$element.val(text);
            }
        }

        if (this.mode === this.MODE_SEARCH) {
            const type = this.$el.find('select.search-type').val();

            this.handleSearchType(type);

            this.$el.find('select.search-type').on('change', () => {
                this.trigger('change');
            });

            this.$element.on('input', () => {
                this.trigger('change');
            });
        }

        if (this.mode === this.MODE_EDIT && !this.autoHeightDisabled) {
            if (!this.autoHeightDisabled) {
                this.controlTextareaHeight();

                this.$element.on('input', () => this.controlTextareaHeight());
            }

            let lastChangeKeydown = new Date();
            const changeKeydownInterval = this.changeInterval * 1000;

            this.$element.on('keydown', () => {
                if (Date.now() - lastChangeKeydown > changeKeydownInterval) {
                    this.trigger('change');
                    lastChangeKeydown = Date.now();
                }
            });
        }
    }

    fetch() {
        const data = {};

        let value = this.$element.val() || null;

        if (value && value.trim() === '') {
            value = '';
        }

        data[this.name] = value

        return data;
    }

    fetchSearch() {
        const type = this.fetchSearchType() || 'startsWith';

        if (type === 'isEmpty') {
            return  {
                type: 'or',
                value: [
                    {
                        type: 'isNull',
                        field: this.name,
                    },
                    {
                        type: 'equals',
                        field: this.name,
                        value: ''
                    }
                ],
                data: {
                    type: type,
                },
            };
        }

        if (type === 'isNotEmpty') {
            return  {
                type: 'and',
                value: [
                    {
                        type: 'notEquals',
                        field: this.name,
                        value: '',
                    },
                    {
                        type: 'isNotNull',
                        field: this.name,
                        value: null,
                    }
                ],
                data: {
                    type: type,
                },
            };
        }

        const value = this.$element.val().toString().trim();

        if (value) {
            return {
                value: value,
                type: type,
            };
        }

        return false;
    }

    getSearchType() {
        return this.getSearchParamsData().type || this.searchParams.typeFront ||
            this.searchParams.type;
    }

    mailTo(emailAddress) {
        const attributes = {
            status: 'Draft',
            to: emailAddress,
        };

        if (
            this.getConfig().get('emailForceUseExternalClient') ||
            this.getPreferences().get('emailUseExternalClient') ||
            !this.getAcl().checkScope('Email', 'create')
        ) {
            Espo.loader.require('email-helper', EmailHelper => {
                const emailHelper = new EmailHelper();

                document.location.href = emailHelper
                    .composeMailToLink(attributes, this.getConfig().get('outboundEmailBccAddress'));
            });

            return;
        }

        const viewName = this.getMetadata().get('clientDefs.' + this.scope + '.modalViews.compose') ||
            'views/modals/compose-email';

        Espo.Ui.notify(' ... ');

        this.createView('quickCreate', viewName, {
            attributes: attributes,
        }, view => {
            view.render();

            Espo.Ui.notify(false);
        });
    }
}

export default TextFieldView;
