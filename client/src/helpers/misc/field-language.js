




class FieldLanguage {

    
    constructor(metadata, language) {
        
        this.metadata = metadata;

        
        this.language = language;
    }

    
    translateAttribute(scope, name) {
        let label = this.language.translate(name, 'fields', scope);

        if (name.indexOf('Id') === name.length - 2) {
            const baseField = name.slice(0, name.length - 2);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('id', 'fields') + ')';
            }
        }
        else if (name.indexOf('Name') === name.length - 4) {
            const baseField = name.slice(0, name.length - 4);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('name', 'fields') + ')';
            }
        }
        else if (name.indexOf('Type') === name.length - 4) {
            const baseField = name.slice(0, name.length - 4);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('type', 'fields') + ')';
            }
        }

        if (name.indexOf('Ids') === name.length - 3) {
            const baseField = name.slice(0, name.length - 3);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('ids', 'fields') + ')';
            }
        }
        else if (name.indexOf('Names') === name.length - 5) {
            const baseField = name.slice(0, name.length - 5);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('names', 'fields') + ')';
            }
        }
        else if (name.indexOf('Types') === name.length - 5) {
            const baseField = name.slice(0, name.length - 5);

            if (this.metadata.get(['entityDefs', scope, 'fields', baseField])) {
                label = this.language.translate(baseField, 'fields', scope) +
                    ' (' + this.language.translate('types', 'fields') + ')';
            }
        }

        return label;
    }
}

export default FieldLanguage;
