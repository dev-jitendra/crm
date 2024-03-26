



import Collection from 'collection';

class TreeCollection extends Collection {

    createSeed() {
        const seed = new this.constructor();

        seed.url = this.url;
        seed.model = this.model;
        seed.name = this.name;
        seed.entityType = this.entityType;
        seed.defs = this.defs;

        return seed;
    }

    prepareAttributes(response, options) {
        const list = super.prepareAttributes(response, options);

        const seed = this.clone();

        seed.reset();

        this.path = response.path;
        
        this.categoryData = response.data || null;

        const f = (l, depth) => {
            l.forEach(d => {
                d.depth = depth;

                const c = this.createSeed();

                if (d.childList) {
                    if (d.childList.length) {
                        f(d.childList, depth + 1);
                        c.set(d.childList);
                        d.childCollection = c;

                        return;
                    }

                    d.childCollection = c;

                    return;
                }

                if (d.childList === null) {
                    d.childCollection = null;

                    return;
                }

                d.childCollection = c;
            });
        };

        f(list, 0);

        return list;
    }

    fetch(options) {
        options = options || {};
        options.data = options.data || {};

        if (this.parentId) {
            options.data.parentId = this.parentId;
        }

        options.data.maxDepth = this.maxDepth;

        return super.fetch(options);
    }
}

export default TreeCollection;
