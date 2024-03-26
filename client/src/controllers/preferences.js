

import RecordController from 'controllers/record';
import Preferences from 'models/preferences';

class PreferencesController extends RecordController {

    defaultAction = 'own'

    getModel(callback, context) {
        const model = new Preferences();

        model.settings = this.getConfig();
        model.defs = this.getMetadata().get('entityDefs.Preferences');

        if (callback) {
            callback.call(this, model);
        }

        return new Promise(resolve => {
            resolve(model);
        });
    }

    checkAccess(action) {
        return true;
    }

    
    actionOwn() {
        this.actionEdit({id: this.getUser().id});
    }

    actionList() {}
}

export default PreferencesController;
