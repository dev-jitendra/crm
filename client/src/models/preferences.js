



import Model from 'model';


class Preferences extends Model {

    name = 'Preferences'
    entityType = 'Preferences'
    urlRoot = 'Preferences'

    
    getDashletOptions(id) {
        const value = this.get('dashletsOptions') || {};

        return value[id] || null;
    }

    
    isPortal() {
        return this.get('isPortalUser');
    }
}

export default Preferences;
