

import ModalView from 'views/modal';

class KanbanMoveOverModalView extends ModalView {

    template = 'modals/kanban-move-over'

    
    backdrop = true

    data() {
        return {
            optionDataList: this.optionDataList,
        };
    }

    events = {
        
        'click [data-action="move"]': function (e) {
            const value = $(e.currentTarget).data('value');

            this.moveTo(value);
        },
    }

    setup() {
        this.scope = this.model.entityType;

        const iconHtml = this.getHelper().getScopeColorIconHtml(this.scope);

        this.statusField = this.options.statusField;

        this.$header = $('<span>');

        this.$header.append(
            $('<span>').text(this.getLanguage().translate(this.scope, 'scopeNames'))
        );

        if (this.model.get('name')) {
            this.$header.append(' <span class="chevron-right"></span> ');
            this.$header.append(
                $('<span>').text(this.model.get('name'))
            )
        }

        this.$header.prepend(iconHtml);

        this.buttonList = [
            {
                name: 'cancel',
                label: 'Cancel'
            }
        ];

        this.optionDataList = [];

        (
            this.getMetadata()
                .get(['entityDefs', this.scope, 'fields', this.statusField, 'options']) || []
        )
            .forEach((item) => {
                this.optionDataList.push({
                    value: item,
                    label: this.getLanguage().translateOption(item, this.statusField, this.scope),
                });
            });
    }

    moveTo(status) {
        const attributes = {};

        attributes[this.statusField] = status;

        this.model
            .save(
                attributes,
                {
                    patch: true,
                    isMoveTo: true,
                }
            )
            .then(() => {
                Espo.Ui.success(this.translate('Done'));
            });

        this.close();
    }
}


export default KanbanMoveOverModalView;
