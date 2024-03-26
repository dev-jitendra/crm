

import RecordController from 'controllers/record';

class RecordTreeController extends RecordController {

    defaultAction = 'listTree'

    beforeView(options) {
        super.beforeView(options);

        options = options || {};

        if (options.model) {
            options.model.unset('childCollection');
            options.model.unset('childList');
        }
    }

    
    beforeListTree() {
        this.handleCheckAccess('read');
    }

    
    actionListTree() {
        this.getCollection().then(collection => {
            collection.url = collection.entityType + '/action/listTree';

            this.main(this.getViewName('listTree'), {
                scope: this.name,
                collection: collection
            });
        });
    }
}

export default RecordTreeController;
