




class ModelFactory {
    
    constructor (metadata) {
        this.metadata = metadata;
    }

    
    dateTime = null

    
    create(entityType, callback, context) {
        return new Promise(resolve => {
            context = context || this;

            this.getSeed(entityType, Seed => {
                const model = new Seed({}, {
                    entityType: entityType,
                    defs: this.metadata.get(['entityDefs', entityType]) || {},
                    dateTime: this.dateTime,
                });

                if (callback) {
                    callback.call(context, model);
                }

                resolve(model);
            });
        });
    }

    
    getSeed(entityType, callback) {
        const className = this.metadata.get(['clientDefs', entityType, 'model']) || 'model';

        Espo.loader.require(className, modelClass => callback(modelClass));
    }
}

export default ModelFactory;
