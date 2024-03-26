



import App from 'app';
import AclPortalManager from 'acl-portal-manager';


class AppPortal extends App {

    aclName = 'aclPortal'
    masterView = 'views/site-portal/master'

    createAclManager() {
        return new AclPortalManager(
            this.user,
            null,
            this.settings.get('aclAllowDeleteCreated')
        );
    }
}

export default AppPortal
