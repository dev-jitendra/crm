

import Controller from 'controller';

class ExternalAccountController extends Controller {

    defaultAction = 'list'

    actionList() {
        this.collectionFactory.create('ExternalAccount', collection => {
            collection.once('sync', () => {
                this.main('ExternalAccount.Index', {
                    collection: collection,
                });
            });

            collection.fetch();
        });
    }

    
    
    actionEdit(options) {
        const id = options.id;

        this.collectionFactory.create('ExternalAccount', collection => {
            collection.once('sync', () => {
                this.main('ExternalAccount.Index', {
                    collection: collection,
                    id: id,
                });
            });

            collection.fetch();
        });
    }
}

export default ExternalAccountController;
