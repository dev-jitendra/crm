

import MultiEnumFieldView from 'views/fields/multi-enum';

class DuplicateFieldListCheckEntityManagerFieldView extends MultiEnumFieldView {

    fieldTypeList = [
        'varchar',
        'personName',
        'email',
        'phone',
        'url',
        'barcode',
    ]

    setupOptions() {
        let entityType = this.model.get('name');

        let options =
            this.getFieldManager()
                .getEntityTypeFieldList(entityType, {
                    typeList: this.fieldTypeList,
                    onlyAvailable: true,
                })
                .sort((a, b) => {
                    return this.getLanguage().translate(a, 'fields', this.entityType)
                        .localeCompare(
                            this.getLanguage().translate(b, 'fields', this.entityType)
                        );
                });

        this.translatedOptions = {};

        options.forEach(item => {
            this.translatedOptions[item] = this.translate(item, 'fields', entityType);
        })

        this.params.options = options;
    }
}

export default DuplicateFieldListCheckEntityManagerFieldView;
