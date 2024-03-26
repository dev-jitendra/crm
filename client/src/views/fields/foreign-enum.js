

import EnumFieldView from 'views/fields/enum';

class ForeignEnumFieldView extends EnumFieldView {

    type = 'foreign'

    setupOptions() {
        this.params.options = [];

        let field = this.params.field;
        let link = this.params.link;

        if (!field || !link) {
            return;
        }

        let scope = this.getMetadata().get(['entityDefs', this.model.entityType, 'links', link, 'entity']);

        if (!scope) {
            return;
        }

        let {
            optionsPath,
            translation,
            options,
            isSorted,
            displayAsLabel,
            style,
        } = this.getMetadata().get(['entityDefs', scope, 'fields', field]);

        options = optionsPath ? this.getMetadata().get(optionsPath) : options;

        this.params.options = Espo.Utils.clone(options) || [];
        this.params.translation = translation;
        this.params.isSorted = isSorted || false;
        this.params.displayAsLabel = displayAsLabel || false;
        this.styleMap = style || {};

        this.translatedOptions = Object.fromEntries(
            this.params.options
                .map(item => [item, this.getLanguage().translateOption(item, field, scope)])
        );
    }
}

export default ForeignEnumFieldView;
