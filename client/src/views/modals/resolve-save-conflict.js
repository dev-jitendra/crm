

import ModalView from 'views/modal';

class ResolveSaveConflictModalView extends ModalView {

    template = 'modals/resolve-save-conflict'

    backdrop = true

    resolutionList = [
        'current',
        'actual',
        'original',
    ]

    defaultResolution = 'current'

    data() {
        const dataList = [];

        this.fieldList.forEach(item => {
            const o = {
                field: item,
                viewKey: item + 'Field',
                resolution: this.defaultResolution,
            };

            dataList.push(o);
        });

        return {
            dataList: dataList,
            entityType: this.entityType,
            resolutionList: this.resolutionList,
        };
    }

    setup() {
        this.headerText = this.translate('Resolve Conflict');

        this.buttonList = [
            {
                name: 'apply',
                label: 'Apply',
                style: 'danger',
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        this.entityType = this.model.entityType;

        this.originalModel = this.model;

        this.originalAttributes = Espo.Utils.cloneDeep(this.options.originalAttributes);
        this.currentAttributes = Espo.Utils.cloneDeep(this.options.currentAttributes);
        this.actualAttributes = Espo.Utils.cloneDeep(this.options.actualAttributes);

        const attributeList = this.options.attributeList;

        const fieldList = [];

        this.getFieldManager()
            .getEntityTypeFieldList(this.entityType)
            .forEach(field => {
                const fieldAttributeList = this.getFieldManager()
                    .getEntityTypeFieldAttributeList(this.entityType, field);

                const intersect = attributeList.filter(value => fieldAttributeList.includes(value));

                if (intersect.length) {
                    fieldList.push(field);
                }
            });

        this.fieldList = fieldList;

        this.wait(
            this.getModelFactory().create(this.entityType)
                .then(model => {
                    this.model = model;

                    this.fieldList.forEach(field => {
                        this.setResolution(field, this.defaultResolution);
                    });

                    this.fieldList.forEach(field => {
                        this.createField(field);
                    });
                })
        );
    }

    setResolution(field, resolution) {
        const attributeList = this.getFieldManager()
            .getEntityTypeFieldAttributeList(this.entityType, field);

        const values = {};

        let source = this.currentAttributes;

        if (resolution === 'actual') {
            source = this.actualAttributes;
        }
        else if (resolution === 'original') {
            source = this.originalAttributes;
        }

        for (const attribute of attributeList) {
            values[attribute] = source[attribute] || null;
        }

        this.model.set(values);
    }

    createField(field) {
        const type = this.model.getFieldType(field);

        const viewName =
            this.model.getFieldParam(field, 'view') ||
            this.getFieldManager().getViewName(type);

        this.createView(field + 'Field', viewName, {
            readOnly: true,
            model: this.model,
            name: field,
            selector: '[data-name="field"][data-field="' + field + '"]',
            mode: 'list',
        });
    }

    afterRender() {
        this.$el.find('[data-name="resolution"]').on('change', e => {
            const $el = $(e.currentTarget);

            const field = $el.attr('data-field');
            const resolution = $el.val();

            this.setResolution(field, resolution);
        });
    }

    
    actionApply() {
        const attributes = this.model.attributes;

        this.originalModel.set(attributes);

        this.trigger('resolve');
        this.close();
    }
}

export default ResolveSaveConflictModalView;
