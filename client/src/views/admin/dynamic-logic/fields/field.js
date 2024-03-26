

define('views/admin/dynamic-logic/fields/field', ['views/fields/multi-enum', 'ui/multi-select'],
function (Dep, MultiSelect) {

    return Dep.extend({

        getFieldList: function () {
            let fields = this.getMetadata().get('entityDefs.' + this.options.scope + '.fields');

            let filterList = Object.keys(fields).filter(field => {
                let fieldType = fields[field].type || null;

                if (
                    fields[field].disabled ||
                    fields[field].utility
                ) {
                    return;
                }

                if (!fieldType) {
                    return;
                }

                if (!this.getMetadata().get(['clientDefs', 'DynamicLogic', 'fieldTypes', fieldType])) {
                    return;
                }

                return true;
            });

            filterList.push('id');

            filterList.sort((v1, v2) => {
                return this.translate(v1, 'fields', this.options.scope)
                    .localeCompare(this.translate(v2, 'fields', this.options.scope));
            });

            return filterList;
        },

        setupTranslatedOptions: function () {
            this.translatedOptions = {};

            this.params.options.forEach(item => {
                this.translatedOptions[item] = this.translate(item, 'fields', this.options.scope);
            });
        },

        setupOptions: function () {
            Dep.prototype.setupOptions.call(this);

            this.params.options = this.getFieldList();
            this.setupTranslatedOptions();
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.$element) {
                MultiSelect.focus(this.$element);
            }
        },
    });
});

