

define('views/email/fields/email-address', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        getAutocompleteMaxCount: function () {
            if (this.autocompleteMaxCount) {
                return this.autocompleteMaxCount;
            }

            return this.getConfig().get('recordsPerPage');
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            this.$input = this.$el.find('input');

            if (this.mode === this.MODE_SEARCH && this.getAcl().check('Email', 'create')) {
                this.initSearchAutocomplete();
            }

            if (this.mode === this.MODE_SEARCH) {
                this.$input.on('input', () => {
                    this.trigger('change');
                });
            }
        },

        initSearchAutocomplete: function () {
            this.$input = this.$input || this.$el.find('input');

            this.$input.autocomplete({
                serviceUrl: () => {
                    return `EmailAddress/search` +
                        `?maxSize=${this.getAutocompleteMaxCount()}`
                },
                paramName: 'q',
                minChars: 1,
                autoSelectFirst: true,
                triggerSelectOnValidInput: false,
                noCache: true,
                formatResult: suggestion => {
                    return this.getHelper().escapeString(suggestion.name) + ' &#60;' +
                        this.getHelper().escapeString(suggestion.id) + '&#62;';
                },
                transformResult: response => {
                    response = JSON.parse(response);

                    let list = response.map(item => {
                        return {
                            id: item.emailAddress,
                            name: item.entityName,
                            emailAddress: item.emailAddress,
                            entityId: item.entityId,
                            entityName: item.entityName,
                            entityType: item.entityType,
                            data: item.emailAddress,
                            value: item.emailAddress,
                        }
                    });

                    if (this.skipCurrentInAutocomplete) {
                        let current = this.$input.val();

                        list = list.filter(item => item.emailAddress !== current)
                    }

                    return {suggestions: list};
                },
                onSelect: (s) => {
                    this.$input.val(s.emailAddress);
                    this.$input.focus();
                },
            });

            this.once('render', () => {
                this.$input.autocomplete('dispose');
            });

            this.once('remove', () => {
                this.$input.autocomplete('dispose');
            });
        },

        fetchSearch: function () {
            let value = this.$element.val();

            if (typeof value.trim === 'function') {
                value = value.trim();
            }

            if (value) {
                return {
                    type: 'equals',
                    value: value,
                };
            }

            return null;
        },
    });
});
