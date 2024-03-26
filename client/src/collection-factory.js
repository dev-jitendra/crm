




class CollectionFactory {
    
    constructor(modelFactory, config, metadata) {
        
        this.modelFactory = modelFactory;
        
        this.metadata = metadata;
        
        this.recordListMaxSizeLimit = config.get('recordListMaxSizeLimit') || 200;
    }

    
    create(entityType, callback, context) {
        return new Promise(resolve => {
            context = context || this;

            this.modelFactory.getSeed(entityType, Model => {
                const orderBy = this.modelFactory.metadata
                    .get(['entityDefs', entityType, 'collection', 'orderBy']);

                const order = this.modelFactory.metadata
                    .get(['entityDefs', entityType, 'collection', 'order']);

                const className = this.modelFactory.metadata
                    .get(['clientDefs', entityType, 'collection']) || 'collection';

                const defs = this.metadata.get(['entityDefs', entityType]) || {};

                Espo.loader.require(className, Collection => {
                    const collection = new Collection(null, {
                        entityType: entityType,
                        orderBy: orderBy,
                        order: order,
                        defs: defs,
                    });

                    collection.model = Model;
                    collection.entityType = entityType;
                    collection.maxMaxSize = this.recordListMaxSizeLimit;

                    if (callback) {
                        callback.call(context, collection);
                    }

                    resolve(collection);
                });
            });
        });
    }
}

export default CollectionFactory;
