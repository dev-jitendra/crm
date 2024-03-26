

import RecordController from 'controllers/record';

class ApiUserController extends RecordController {

    entityType ='User'

    getCollection(usePreviouslyFetched) {
        return super.getCollection()
            .then(collection => {
                collection.data.userType = 'api';

                return collection;
            });
    }

    
    createViewView(options, model, view) {
        if (!model.isApi()) {
            if (model.isPortal()) {
                this.getRouter().dispatch('PortalUser', 'view', {id: model.id, model: model});

                return;
            }

            this.getRouter().dispatch('User', 'view', {id: model.id, model: model});

            return;
        }

        super.createViewView(options, model, view);
    }

    actionCreate(options) {
        options = options || {};
        options.attributes = options.attributes  || {};
        options.attributes.type = 'api';

        super.actionCreate(options);
    }
}

export default ApiUserController;
