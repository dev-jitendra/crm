

import View from 'view';
import Select from 'ui/select';

export default class extends View {

    template = 'admin/dynamic-logic/conditions/field-types/base'

    events = {
        'click > div > div > [data-action="remove"]': function (e) {
            e.stopPropagation();

            this.trigger('remove-item');
        },
    }

    data() {
        return {
            type: this.type,
            field: this.field,
            scope: this.scope,
            typeList: this.typeList,
            leftString: this.translateLeftString(),
        };
    }

    translateLeftString() {
        return this.translate(this.field, 'fields', this.scope);
    }

    setup() {
        this.type = this.options.type;
        this.field = this.options.field;
        this.scope = this.options.scope;
        this.fieldType = this.options.fieldType;

        this.itemData = this.options.itemData;
        this.additionalData = (this.itemData.data || {});

        this.typeList = this.getMetadata()
            .get(['clientDefs', 'DynamicLogic', 'fieldTypes', this.fieldType, 'typeList']);

        this.wait(true);

        this.createModel().then(model => {
            this.model = model;

            this.populateValues();
            this.manageValue();

            this.wait(false);
        });
    }

    createModel() {
        return this.getModelFactory().create(this.scope);
    }

    afterRender() {
        this.$type = this.$el.find('select[data-name="type"]');

        Select.init(this.$type.get(0));

        this.$type.on('change', () => {
            this.type = this.$type.val();

            this.manageValue();
        });
    }

    populateValues() {
        if (this.itemData.attribute) {
            this.model.set(this.itemData.attribute, this.itemData.value);
        }

        this.model.set(this.additionalData.values || {});
    }

    getValueViewName() {
        const fieldType = this.getMetadata()
            .get(['entityDefs', this.scope, 'fields', this.field, 'type']) || 'base';

        return this.getMetadata().get(['entityDefs', this.scope, 'fields', this.field, 'view']) ||
            this.getFieldManager().getViewName(fieldType);
    }

    getValueFieldName() {
        return this.field;
    }

    manageValue() {
        const valueType = this.getMetadata()
            .get(['clientDefs', 'DynamicLogic', 'fieldTypes', this.fieldType, 'conditionTypes',
                this.type, 'valueType']) ||
            this.getMetadata() .get(['clientDefs', 'DynamicLogic', 'conditionTypes', this.type, 'valueType']);

        if (valueType === 'field') {
            const viewName = this.getValueViewName();
            const fieldName = this.getValueFieldName();

            this.createView('value', viewName, {
                model: this.model,
                name: fieldName,
                selector: '.value-container',
                mode: 'edit',
                readOnlyDisabled: true,
            }, view => {
                if (this.isRendered()) {
                    view.render();
                }
            });
        }
        else if (valueType === 'custom') {
            this.clearView('value');

            const methodName = 'createValueView' + Espo.Utils.upperCaseFirst(this.type);

            this[methodName]();
        }
        else if (valueType === 'varchar') {
            this.createView('value', 'views/fields/varchar', {
                model: this.model,
                name: this.getValueFieldName(),
                selector: '.value-container',
                mode: 'edit',
                readOnlyDisabled: true,
            }, (view) => {
                if (this.isRendered()) {
                    view.render();
                }
            });
        }
        else {
            this.clearView('value');
        }
    }

    fetch() {
        const valueView = this.getView('value');

        const item = {
            type: this.type,
            attribute: this.field,
        };

        if (valueView) {
            valueView.fetchToModel();

            item.value = this.model.get(this.field);
        }

        return item;
    }
}
