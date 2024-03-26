

import RecordController from 'controllers/record';

class UserController extends RecordController {

    getCollection(usePreviouslyFetched) {
        return super.getCollection()
            .then(collection => {
                collection.data.userType = 'internal';

                return collection;
            });
    }

    
    createViewView(options, model, view) {
        if (model.get('deleted')) {
            view = 'views/deleted-detail';

            super.createViewView(options, model, view);

            return;
        }

        if (model.isPortal()) {
            this.getRouter().dispatch('PortalUser', 'view', {id: model.id, model: model});

            return;
        }

        if (model.isApi()) {
            this.getRouter().dispatch('ApiUser', 'view', {id: model.id, model: model});

            return;
        }

        super.createViewView(options, model, view);
    }
}

export default UserController;
