

import Acl from 'acl';

class NotificationAcl extends Acl {

    checkIsOwner(model) {
        if (this.getUser().id === model.get('userId')) {
            return true;
        }

        return false;
    }
}

export default NotificationAcl;
