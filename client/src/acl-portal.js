



import Acl from 'acl';


class AclPortal extends Acl {

    
    checkScope(data, action, precise, entityAccessData) {
        entityAccessData = entityAccessData || {};

        let inAccount = entityAccessData.inAccount;
        let isOwnContact = entityAccessData.isOwnContact;
        let isOwner = entityAccessData.isOwner;

        if (this.getUser().isAdmin()) {
            return true;
        }

        if (data === false) {
            return false;
        }

        if (data === true) {
            return true;
        }

        if (typeof data === 'string') {
            return true;
        }

        if (data === null) {
            return false;
        }

        action = action || null;

        if (action === null) {
            return true;
        }

        if (!(action in data)) {
            return false;
        }

        var value = data[action];

        if (value === 'all') {
            return true;
        }

        if (value === 'yes') {
            return true;
        }

        if (value === 'no') {
            return false;
        }

        if (typeof isOwner === 'undefined') {
            return true;
        }

        if (isOwner) {
            if (value === 'own' || value === 'account' || value === 'contact') {
                return true;
            }
        }

        var result = false;

        if (value === 'account') {
            result = inAccount;
            if (inAccount === null) {
                if (precise) {
                    result = null;
                }
                else {
                    return true;
                }
            }
            else if (inAccount) {
                return true;
            }
        }

        if (value === 'contact') {
            result = isOwnContact;

            if (isOwnContact === null) {
                if (precise) {
                    result = null;
                }
                else {
                    return true;
                }
            }
            else if (isOwnContact) {
                return true;
            }
        }

        if (isOwner === null) {
            if (precise) {
                result = null;
            }
            else {
                return true;
            }
        }

        return result;
    }

    
    checkModel(model, data, action, precise) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        let entityAccessData = {
            isOwner: this.checkIsOwner(model),
            inAccount: this.checkInAccount(model),
            isOwnContact: this.checkIsOwnContact(model),
        };

        return this.checkScope(data, action, precise, entityAccessData);
    }

    
    checkIsOwner(model) {
        if (model.hasField('createdBy')) {
            if (this.getUser().id === model.get('createdById')) {
                return true;
            }
        }

        return false;
    }

    
    checkInAccount(model) {
        let accountIdList = this.getUser().getLinkMultipleIdList('accounts');

        if (!accountIdList.length) {
            return false;
        }

        if (model.hasField('account')) {
            if (model.get('accountId')) {
                if (~accountIdList.indexOf(model.get('accountId'))) {
                    return true;
                }
            }
        }

        var result = false;

        if (model.hasField('accounts') && model.hasLink('accounts')) {
            if (!model.has('accountsIds')) {
                result = null;
            }

            (model.getLinkMultipleIdList('accounts')).forEach(id => {
                if (~accountIdList.indexOf(id)) {
                    result = true;
                }
            });
        }

        if (model.hasField('parent') && model.hasLink('parent')) {
            if (model.get('parentType') === 'Account') {
                if (!accountIdList.indexOf(model.get('parentId'))) {
                    return true;
                }
            }
        }

        if (result === false) {
            if (!model.hasField('accounts') && model.hasLink('accounts')) {
                return true;
            }
        }

        return result;
    }

    
    checkIsOwnContact(model) {
        let contactId = this.getUser().get('contactId');

        if (!contactId) {
            return false;
        }

        if (model.hasField('contact')) {
            if (model.get('contactId')) {
                if (contactId === model.get('contactId')) {
                    return true;
                }
            }
        }

        let result = false;

        if (model.hasField('contacts') && model.hasLink('contacts')) {
            if (!model.has('contactsIds')) {
                result = null;
            }

            (model.getLinkMultipleIdList('contacts')).forEach(id => {
                if (contactId === id) {
                    result = true;
                }
            });
        }

        if (model.hasField('parent') && model.hasLink('parent')) {
            if (model.get('parentType') === 'Contact' && model.get('parentId') === contactId) {
                return true;
            }
        }

        if (result === false) {
            if (!model.hasField('contacts') && model.hasLink('contacts')) {
                return true;
            }
        }

        return result;
    }
}

export default AclPortal;
