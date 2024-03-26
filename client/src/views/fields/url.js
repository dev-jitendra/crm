



import VarcharFieldView from 'views/fields/varchar';

class UrlFieldView extends VarcharFieldView {

    type = 'url'

    listTemplate = 'fields/url/list'
    detailTemplate = 'fields/url/detail'
    defaultProtocol = 'https:'

    validations = [
        'required',
        'valid',
        'maxLength',
    ]

    noSpellCheck = true

    DEFAULT_MAX_LENGTH =255

    data() {
        const data = super.data();

        data.url = this.getUrl();

        return data;
    }

    afterRender() {
        super.afterRender();

        if (this.isEditMode()) {
            this.$element.on('change', () => {
                const value = this.$element.val() || '';

                const parsedValue = this.parse(value);

                if (parsedValue === value) {
                    return;
                }

                const decoded = parsedValue ? decodeURI(parsedValue) : '';

                this.$element.val(decoded);
            });
        }
    }

    getValueForDisplay() {
        const value = this.model.get(this.name);

        return value ? decodeURI(value) : null;
    }

    
    parse(value) {
        value = value.trim();

        if (this.params.strip) {
            value = this.strip(value);
        }

        if (value === decodeURI(value)) {
            value = encodeURI(value);
        }

        return value;
    }

    
    strip(value) {
        if (value.indexOf('
            value = value.substring(value.indexOf('
        }

        value = value.replace(/\/+$/, '');

        return value;
    }

    getUrl() {
        let url = this.model.get(this.name);

        if (url && url !== '') {
            if (url.indexOf('
                url = this.defaultProtocol + '
            }

            return url;
        }

        return url;
    }

    
    validateValid() {
        const value = this.model.get(this.name);

        if (!value) {
            return false;
        }

        
        const pattern = this.getMetadata().get(['app', 'regExpPatterns', 'uriOptionalProtocol', 'pattern']);

        const regExp = new RegExp('^' + pattern + '$');

        if (regExp.test(value)) {
            return false;
        }

        const msg = this.translate('fieldInvalid', 'messages')
            .replace('{field}', this.translate(this.name, 'fields', this.entityType));

        this.showValidationMessage(msg);

        return true;
    }

    
    validateMaxLength() {
        const maxLength = this.params.maxLength || this.DEFAULT_MAX_LENGTH;

        const value = this.model.get(this.name);

        if (!value || !value.length) {
            return false;
        }

        if (value.length <= maxLength) {
            return false;
        }

        const msg = this.translate('fieldUrlExceedsMaxLength', 'messages')
            .replace('{maxLength}', maxLength)
            .replace('{field}', this.translate(this.name, 'fields', this.entityType));

        this.showValidationMessage(msg);

        return true;
    }

    fetch() {
        const data = super.fetch();

        const value = data[this.name];

        if (!value) {
            return data;
        }

        data[this.name] = this.parse(value);

        return data;
    }
}

export default UrlFieldView;
