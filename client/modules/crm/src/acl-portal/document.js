

import AclPortal from 'acl-portal';

class DocumentAclPortal extends AclPortal {

    
    checkModelEdit(model, data, precise) {
        let result = this.checkModel(model, data, 'delete', precise);

        if (result) {
            return true;
        }

        if (data.edit === 'account') {
            return true;
        }

        return false;
    }
}

export default DocumentAclPortal;
