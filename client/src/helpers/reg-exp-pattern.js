


class RegExpPatternHelper {

    
    constructor(metadata, language) {
        
        this.metadata = metadata;
        
        this.language = language;
    }

    
    validate(pattern, value, field, entityType) {
        if (value === '' || value === null) {
            return null;
        }

        let messageKey = 'fieldNotMatchingPattern';

        if (pattern[0] === '$') {
            const patternName = pattern.slice(1);
            const foundPattern = this.metadata.get(['app', 'regExpPatterns', patternName, 'pattern']);

            if (foundPattern) {
                messageKey += '$' + patternName;
                pattern = foundPattern;
            }
        }

        const regExp = new RegExp('^' + pattern + '$');

        if (regExp.test(value)) {
            return null;
        }

        let message = this.language.translate(messageKey, 'messages')
            .replace('{pattern}', pattern);

        if (field && entityType) {
            message = message.replace('{field}', this.language.translate(field, 'fields', entityType));
        }

        return {message: message};
    }
}

export default RegExpPatternHelper;
