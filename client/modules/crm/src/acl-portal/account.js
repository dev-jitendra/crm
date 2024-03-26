

import AclPortal from 'acl-portal';

class AccountAclPortal extends AclPortal {

    checkInAccount(model) {
        const accountIdList = this.getUser().getLinkMultipleIdList('accounts');

        if (!accountIdList.length) {
            return false;
        }

        if (~accountIdList.indexOf(model.id)) {
            return true;
        }

        return false;
    }
}

export default AccountAclPortal;
