

import Controller from 'controller';

class AddressMapController extends Controller {

    defaultAction = 'index'

    
    actionIndex() {
        this.error404();
    }

    
    
    actionView(o) {
        this.modelFactory
            .create(o.entityType)
            .then(model => {
                model.id = o.id;

                model.fetch()
                    .then(() => {
                        const viewName = this.getMetadata().get(['AddressMap', 'view']) ||
                            'views/address-map/view';

                        this.main(viewName, {
                            model: model,
                            field: o.field,
                        });
                    });
            });
    }
}

export default AddressMapController;
