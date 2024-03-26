

import ChecklistFieldView from 'views/fields/checklist';

class ForeignChecklistFieldView extends ChecklistFieldView {

    type = 'foreign'

    setupOptions() {
        this.params.options = [];

        if (!this.params.field || !this.params.link) {
            return;
        }

        const scope = this.getMetadata()
            .get(['entityDefs', this.model.entityType, 'links', this.params.link, 'entity']);

        if (!scope) {
            return;
        }

        this.params.isSorted = this.getMetadata()
            .get(['entityDefs', scope, 'fields', this.params.field, 'isSorted']) || false;

        this.params.options = this.getMetadata()
            .get(['entityDefs', scope, 'fields', this.params.field, 'options']) || [];

        this.translatedOptions = {};

        this.params.options.forEach(item => {
            this.translatedOptions[item] = this.getLanguage()
                .translateOption(item, this.params.field, scope);
        });
    }
}

export default ForeignChecklistFieldView;
