

import EmailAddressFieldView from 'views/fields/email-address';

class SettingsOutboundEmailFromAddressFieldView extends EmailAddressFieldView {

    useAutocompleteUrl = true

    getAutocompleteUrl(q) {
        return 'InboundEmail?searchParams=' + JSON.stringify({
            select: ['emailAddress'],
            maxSize: 7,
            where: [
                {
                    type: 'startsWith',
                    attribute: 'emailAddress',
                    value: q,
                },
                {
                    type: 'isTrue',
                    attribute: 'useSmtp',
                },
            ],
        });
    }

    transformAutocompleteResult(response) {
        const result = super.transformAutocompleteResult(response);

        result.suggestions.forEach(item => {
            item.value = item.attributes.emailAddress;
        });

        return result;
    }
}

export default SettingsOutboundEmailFromAddressFieldView;
