



import Collection from 'collection';


class MultiCollection extends Collection {

    
    seeds = null

    
    prepareAttributes(response, options) {
        this.total = response.total;

        if (!('list' in response)) {
            throw new Error("No 'list' in response.");
        }

        
        const list = response.list;

        return list.map(attributes => {
            const entityType = attributes._scope;

            if (!entityType) {
                throw new Error("No '_scope' attribute.");
            }

            attributes = _.clone(attributes);
            delete attributes['_scope'];

            const model = this.seeds[entityType].clone();

            model.set(attributes);

            return model;
        });
    }

    
    clone() {
        const collection = super.clone();
        collection.seeds = this.seeds;

        return collection;
    }
}


export default MultiCollection;
