

import RecordController from 'controllers/record';

class PortalUserController extends RecordController {

    entityType = 'User'

    getCollection(usePreviouslyFetched) {
        return super.getCollection()
            .then(collection => {
                collection.data.userType = 'portal';

                return collection;
            });
    }

    
    createViewView(options, model, view) {
        if (!model.isPortal()) {
            if (model.isApi()) {
                this.getRouter().dispatch('ApiUser', 'view', {id: model.id, model: model});

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
        options.attributes.type = 'portal';

        super.actionCreate(options);
    }

    checkAccess(action) {
        if (this.getAcl().getPermissionLevel('portalPermission') === 'yes') {
            return true;
        }

        return false;
    }
}

export default PortalUserController;
