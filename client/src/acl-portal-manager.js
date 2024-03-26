



import AclManager from 'acl-manager';
import AclPortal from 'acl-portal';


class AclPortalManager extends AclManager {

    
    
    checkInAccount(model) {
        const impl =
            
            this.getImplementation(model.entityType);

        return impl.checkInAccount(model);
    }

    
    
    checkIsOwnContact(model) {
        const impl =
            
            this.getImplementation(model.entityType);

        return impl.checkIsOwnContact(model);
    }

    
    getImplementation(scope) {
        if (!(scope in this.implementationHash)) {
            let implementationClass = AclPortal;

            if (scope in this.implementationClassMap) {
                implementationClass = this.implementationClassMap[scope];
            }

            this.implementationHash[scope] =
                new implementationClass(this.getUser(), scope, this.aclAllowDeleteCreated);
        }

        return this.implementationHash[scope];
    }
}

export default AclPortalManager;
