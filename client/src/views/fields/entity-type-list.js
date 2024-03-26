

import MultiEnumFieldView from 'views/fields/multi-enum';

class EntityTypeListFieldView extends MultiEnumFieldView {

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
    }

    setup() {
        if (!this.params.translation) {
            this.params.translation = 'Global.scopeNames';
        }

        this.setupOptions();

        super.setup();
    }
}

export default EntityTypeListFieldView;
