

import NoteStreamView from 'views/stream/note';

class UpdateNoteStreamView extends NoteStreamView {

    template = 'stream/notes/update'
    messageName = 'update'

    data() {
        return {
            ...super.data(),
            fieldsArr: this.fieldsArr,
            parentType: this.model.get('parentType'),
        };
    }

    init() {
        if (this.getUser().isAdmin()) {
            this.isRemovable = true;
        }

        super.init();
    }

    setup() {
        this.addActionHandler('expandDetails', (e, target) => this.toggleDetails(e, target));

        this.createMessage();

        this.wait(true);

        this.getModelFactory().create(this.model.get('parentType'), model => {
            let modelWas = model;
            let modelBecame = model.clone();

            let data = this.model.get('data');

            data.attributes = data.attributes || {};

            modelWas.set(data.attributes.was);
            modelBecame.set(data.attributes.became);

            this.fieldsArr = [];

            let fields = data.fields;

            fields.forEach(field => {
                let type = model.getFieldType(field) || 'base';
                let viewName = this.getMetadata().get(['entityDefs', model.entityType, 'fields', field, 'view']) ||
                    this.getFieldManager().getViewName(type);

                let attributeList = this.getFieldManager().getEntityTypeFieldAttributeList(model.entityType, field);

                let hasValue = false;

                for (let attribute of attributeList) {
                    if (attribute in data.attributes.was) {
                        hasValue = true;

                        break;
                    }
                }

                if (!hasValue) {
                    this.fieldsArr.push({
                        field: field,
                        noValues: true,
                    });

                    return;
                }

                this.createView(field + 'Was', viewName, {
                    model: modelWas,
                    readOnly: true,
                    defs: {
                        name: field
                    },
                    mode: 'detail',
                    inlineEditDisabled: true,
                });

                this.createView(field + 'Became', viewName, {
                    model: modelBecame,
                    readOnly: true,
                    defs: {
                        name: field,
                    },
                    mode: 'detail',
                    inlineEditDisabled: true,
                });

                this.fieldsArr.push({
                    field: field,
                    was: field + 'Was',
                    became: field + 'Became',
                });
            });

            this.wait(false);
        });
    }

    
    toggleDetails(event, target) {
        if (this.$el.find('.details').hasClass('hidden')) {
            this.$el.find('.details').removeClass('hidden');

            $(target).find('span')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');

            return;
        }

        this.$el.find('.details').addClass('hidden');

        $(target).find('span')
            .addClass('fa-chevron-down')
            .removeClass('fa-chevron-up');
    }
}

export default UpdateNoteStreamView;
