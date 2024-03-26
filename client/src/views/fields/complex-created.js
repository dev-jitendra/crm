

import BaseFieldView from 'views/fields/base';

class ComplexCreatedFieldView extends BaseFieldView {

    
    detailTemplateContent =
        `<span data-name="{{baseName}}At" class="field">{{{atField}}}</span>
        <span class="text-muted chevron-right"></span>
        <span data-name="{{baseName}}By" class="field">{{{byField}}}</span>`

    baseName = 'created'

    getAttributeList() {
        return [this.fieldAt, this.fieldBy];
    }

    init() {
        this.baseName = this.options.baseName || this.baseName;
        this.fieldAt = this.baseName + 'At';
        this.fieldBy = this.baseName + 'By';

        super.init();
    }

    setup() {
        super.setup();

        this.createField('at');
        this.createField('by');
    }

    data() {
        return {
            baseName: this.baseName,
            ...super.data(),
        };
    }

    createField(part) {
        let field = this.baseName + Espo.Utils.upperCaseFirst(part);

        let type = this.model.getFieldType(field) || 'base';

        let viewName = this.model.getFieldParam(field, 'view') ||
            this.getFieldManager().getViewName(type);

        this.createView(part + 'Field', viewName, {
            name: field,
            model: this.model,
            mode: this.MODE_DETAIL,
            readOnly: true,
            readOnlyLocked: true,
            selector: '[data-name="' + field + '"]',
        });
    }

    fetch() {
        return {};
    }
}

export default ComplexCreatedFieldView;
