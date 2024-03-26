

import AclPortal from 'acl-portal';

class ContactAclPortal extends AclPortal {

    checkIsOwnContact(model) {
        const contactId = this.getUser().get('contactId');

        if (!contactId) {
            return false;
        }

        if (contactId === model.id) {
            return true;
        }

        return false;
    }
}

export default ContactAclPortal;
