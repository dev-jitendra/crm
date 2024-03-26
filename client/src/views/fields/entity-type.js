

import EnumFieldView from 'views/fields/enum';

class EntityTypeFieldView extends EnumFieldView {

    checkAvailability(entityType) {
        const defs = this.scopesMetadataDefs[entityType] || {};

        if (defs.entity && defs.object) {
            return true;
        }
    }

    setupOptions() {
        const scopes = this.scopesMetadataDefs = this.getMetadata().get('scopes');

        this.params.options = Object.keys(scopes)
            .filter(scope => {
                if (this.checkAvailability(scope)) {
                    return true;
                }
            })
            .sort((v1, v2) => {
                 return this.translate(v1, 'scopeNames')
                     .localeCompare(this.translate(v2, 'scopeNames'));
            });

        this.params.options.unshift('');
    }

    setup() {
        this.params.translation = 'Global.scopeNames';
        this.setupOptions();

        super.setup();
    }
}

export default EntityTypeFieldView;
