

import Controller from 'controller';

class ActivitiesController extends Controller {

    checkAccess(action) {
        if (this.getAcl().check('Activities')) {
            return true;
        }

        return false;
    }

    
    actionActivities(options) {
        this.processList('activities', options.entityType, options.id, options.targetEntityType);
    }

    actionHistory(options) {
        this.processList('history', options.entityType, options.id, options.targetEntityType);
    }

    
    processList(type, entityType, id, targetEntityType) {
        let viewName = 'crm:views/activities/list'

        let model;

        this.modelFactory.create(entityType)
            .then(m => {
                model = m;
                model.id = id;

                return model.fetch({main: true});
            })
            .then(() => {
                return this.collectionFactory.create(targetEntityType);
            })
            .then(collection => {
                collection.url = 'Activities/' + model.entityType + '/' + id + '/' +
                    type + '/list/' + targetEntityType;

                this.main(viewName, {
                    scope: entityType,
                    model: model,
                    collection: collection,
                    link:  type + '_' + targetEntityType,
                    type: type,
                });
            });
    }
}

export default ActivitiesController;
