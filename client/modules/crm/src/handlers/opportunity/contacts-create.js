

import CreateRelated from 'handlers/create-related';

class ContactsCreateHandler extends CreateRelated {

    getAttributes(model) {
        const attributes = {};

        if (model.get('accountId')) {
            attributes['accountsIds'] = [model.get('accountId')]
        }

        return Promise.resolve(attributes);
    }
}

export default ContactsCreateHandler;
