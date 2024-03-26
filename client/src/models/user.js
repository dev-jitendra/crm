



import Model from 'model';


class User extends Model {

    name = 'User'
    entityType = 'User'
    urlRoot = 'User'

    
    isAdmin() {
        return this.get('type') === 'admin' || this.isSuperAdmin();
    }

    
    isPortal() {
        return this.get('type') === 'portal';
    }

    
    isApi() {
        return this.get('type') === 'api';
    }

    
    isRegular() {
        return this.get('type') === 'regular';
    }

    
    isSystem() {
        return this.get('type') === 'system';
    }

    
    isSuperAdmin() {
        return this.get('type') === 'super-admin';
    }
}

export default User;
