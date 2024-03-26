

define('views/admin/field-manager/fields/options-reference', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        enumFieldTypeList: [
            'enum',
            'multiEnum',
            'array',
            'checklist',
            'varchar',
        ],

        setupOptions: function () {
            this.params.options = [''];

            let entityTypeList = Object.keys(this.getMetadata().get(['entityDefs']))
                .filter(item => this.getMetadata().get(['scopes', item, 'object']))
                .sort((s1, s2) => {
                    return this.getLanguage().translate(s1, 'scopesName')
                        .localeCompare(this.getLanguage().translate(s2, 'scopesName'));
                });

            this.translatedOptions = {};

            entityTypeList.forEach(entityType => {
                let fieldList =
                    Object.keys(this.getMetadata().get(['entityDefs', entityType, 'fields']) || [])
                        .filter(item => entityType !== this.model.scope || item !== this.model.get('name'))
                        .sort((s1, s2) => {
                            return this.getLanguage().translate(s1, 'fields', entityType)
                                .localeCompare(this.getLanguage().translate(s2, 'fields', entityType));
                        });

                fieldList.forEach(field => {
                    let {type, options, optionsPath, optionsReference} =
                        this.getMetadata().get(['entityDefs', entityType, 'fields', field]) || {};

                    if (!this.enumFieldTypeList.includes(type)) {
                        return;
                    }

                    if (optionsPath || optionsReference) {
                        return;
                    }

                    if (!options) {
                        return;
                    }

                    let value = entityType + '.' + field;

                    this.params.options.push(value);

                    this.translatedOptions[value] =
                        this.translate(entityType, 'scopeName') + ' · ' +
                        this.translate(field, 'fields', entityType);
                });
            });
        },
    });
});
