



import Collection from 'collection';

class NoteCollection extends Collection {

    
    prepareAttributes(response, params) {
        const total = this.total;

        const list = super.prepareAttributes(response, params);

        if (params.data && params.data.after) {
            if (total >= 0 && response.total >= 0) {
                this.total = total + response.total;
            } else {
                this.total = total;
            }
        }

        return list;
    }

    
    fetchNew(options) {
        options = options || {};

        options.data = options.data || {};
        options.fetchNew = true;
        options.noRebuild = true;
        options.lengthBeforeFetch = this.length;

        if (this.length) {
            options.data.after = this.models[0].get('createdAt');
            options.remove = false;
            options.at = 0;
            options.maxSize = null;
        }

        return this.fetch(options);
    }
}

export default NoteCollection;
