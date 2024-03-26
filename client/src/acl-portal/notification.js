

import AclPortal from 'acl-portal';

class NotificationAclPortal extends AclPortal {

    checkIsOwner(model) {
        if (this.getUser().id === model.get('userId')) {
            return true;
        }

        return false;
    }
}

export default NotificationAclPortal;
