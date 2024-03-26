



import Model from 'model';
import {Events, View as BullView} from 'bullbone';
import _ from 'underscore';








class Collection {

    
    entityType = null

    
    total = 0

    
    offset = 0

    
    maxSize = 20

    
    order = null

    
    orderBy = null

    
    where = null

    
    whereAdditional = null

    
    lengthCorrection = 0

    
    maxMaxSize = 0

    
    whereFunction

    
    lastSyncPromise = null

    
    constructor(models, options) {
        options = {...options};

        if (options.model) {
            this.model = options.model;
        }

        this._reset();

        if (options.entityType) {
            this.entityType = options.entityType;
            
            this.name = this.entityType;
        }

        
        this.urlRoot = options.urlRoot || this.urlRoot || this.entityType;

        
        this.url = options.url || this.url || this.urlRoot;

        this.orderBy = options.orderBy || this.orderBy;
        this.order = options.order || this.order;

        this.defaultOrder = this.order;
        this.defaultOrderBy = this.orderBy;

        
        this.defs = options.defs || {};

        this.data = {};

        
        this.model = options.model || Model;

        if (models) {
            this.reset(models, {silent: true, ...options});
        }
    }

    
    add(models, options) {
        this.set(models, {merge: false, ...options, ...addOptions});

        return this;
    }

    
    remove(models, options) {
        options = {...options};

        const singular = !_.isArray(models);

        models = singular ? [models] : models.slice();

        const removed = this._removeModels(models, options);

        if (!options.silent && removed.length) {
            options.changes = {
                added: [],
                merged: [],
                removed: removed,
            };

            this.trigger('update', this, options);
        }

        return this;
    }

    
    set(models, options) {
        if (models == null) {
            return [];
        }

        options = {...setOptions, ...options};

        if (options.prepare && !this._isModel(models)) {
            models = this.prepareAttributes(models, options) || [];
        }

        const singular = !_.isArray(models);
        models = singular ? [models] : models.slice();

        let at = options.at;

        if (at != null) {
            at = +at;
        }

        if (at > this.length) {
            at = this.length;
        }

        if (at < 0) {
            at += this.length + 1;
        }

        const set = [];
        const toAdd = [];
        const toMerge = [];
        const toRemove = [];
        const modelMap = {};

        const add = options.add;
        const merge = options.merge;
        const remove = options.remove;

        let model, i;

        for (i = 0; i < models.length; i++) {
            model = models[i];

            const existing = this._get(model);

            if (existing) {
                if (merge && model !== existing) {
                    let attributes = this._isModel(model) ?
                        model.attributes :
                        model;

                    if (options.prepare) {
                        attributes = existing.prepareAttributes(attributes, options);
                    }

                    existing.set(attributes, options);
                    toMerge.push(existing);
                }

                if (!modelMap[existing.cid]) {
                    modelMap[existing.cid] = true;
                    set.push(existing);
                }

                models[i] = existing;
            }
            else if (add) {
                model = models[i] = this._prepareModel(model);

                if (model) {
                    toAdd.push(model);

                    this._addReference(model, options);

                    modelMap[model.cid] = true;
                    set.push(model);
                }
            }
        }

        
        if (remove) {
            for (i = 0; i < this.length; i++) {
                model = this.models[i];

                if (!modelMap[model.cid]) {
                    toRemove.push(model);
                }
            }

            if (toRemove.length) {
                this._removeModels(toRemove, options);
            }
        }

        let orderChanged = false;
        const replace = add && remove;

        if (set.length && replace) {
            orderChanged =
                this.length !== set.length ||
                _.some(this.models, (m, index) => {
                    return m !== set[index];
                });

            this.models.length = 0;
            splice(this.models, set, 0);

            this.length = this.models.length;
        }
        else if (toAdd.length) {
            splice(this.models, toAdd, at == null ? this.length : at);

            this.length = this.models.length;
        }

        if (!options.silent) {
            for (i = 0; i < toAdd.length; i++) {
                if (at != null) {
                    options.index = at + i;
                }

                model = toAdd[i];

                model.trigger('add', model, this, options);
            }

            if (orderChanged) {
                this.trigger('sort', this, options);
            }

            if (toAdd.length || toRemove.length || toMerge.length) {
                options.changes = {
                    added: toAdd,
                    removed: toRemove,
                    merged: toMerge
                };

                this.trigger('update', this, options);
            }
        }

        return models;
    }

    
    reset(models, options) {
        this.lengthCorrection = 0;

        options = options ? _.clone(options) : {};

        for (let i = 0; i < this.models.length; i++) {
            this._removeReference(this.models[i], options);
        }

        options.previousModels = this.models;

        this._reset();

        if (models) {
            this.add(models, {silent: true, ...options});
        }

        if (!options.silent) {
            this.trigger('reset', this, options);
        }

        return this;
    }

    
    push(model, options) {
        this.add(model, {at: this.length, ...options});

        return this;
    }

    
    pop(options) {
        const model = this.at(this.length - 1);

        if (!model) {
            return null;
        }

        this.remove(model, options);

        return model;
    }

    
    unshift(model, options) {
        this.add(model, {at: 0, ...options});

        return this;
    }

    
    shift(options) {
        const model = this.at(0);

        if (!model) {
            return null;
        }

        this.remove(model, options);

        return model;
    }

    
    get(id) {
        return this._get(id);
    }

    
    has(id) {
        return this._has(id);
    }

    
    at(index) {
        if (index < 0) {
            index += this.length;
        }

        return this.models[index];
    }

    
    forEach(callback, context) {
        return this.models.forEach(callback, context);
    }

    
    indexOf(model) {
        return this.models.indexOf(model);
    }

    
    _has(obj) {
        return !!this._get(obj)
    }

    
    _get(obj) {
        if (obj == null) {
            return void 0;
        }

        return this._byId[obj] ||
            this._byId[this.modelId(obj.attributes || obj)] ||
            obj.cid && this._byId[obj.cid];
    }

    
    modelId(attributes) {
        return attributes['id'];
    }

    
    _reset() {
        
        this.length = 0;

        
        this.models = [];

        
        this._byId  = {};
    }

    
    sort(orderBy, order) {
        this.orderBy = orderBy;

        if (order === true) {
            order = 'desc';
        }
        else if (order === false) {
            order = 'asc';
        }

        this.order = order || 'asc';

        return this.fetch();
    }

    
    nextPage() {
        this.setOffset(this.offset + this.maxSize);
    }

    
    previousPage() {
        this.setOffset(this.offset - this.maxSize);
    }

    
    firstPage() {
        this.setOffset(0);
    }

    
    lastPage() {
        let offset = this.total - this.total % this.maxSize;

        if (offset === this.total) {
            offset = this.total - this.maxSize;
        }

        this.setOffset(offset);
    }

    
    setOffset(offset) {
        if (offset < 0) {
            throw new RangeError('offset can not be less than 0');
        }

        if (offset > this.total && this.total !== -1 && offset > 0) {
            throw new RangeError('offset can not be larger than total count');
        }

        this.offset = offset;
        this.fetch();
    }

    
    hasMore() {
        return this.total > this.length || this.total === -1;
    }

    
    prepareAttributes(response, options) {
        this.total = response.total;
        this.dataAdditional = response.additionalData || null;

        return response.list;
    }

    
    parse(response, options) {
        return this.prepareAttributes(response, options);
    }

    
    fetch(options) {
        options = {...options};

        options.data = {...options.data, ...this.data};

        this.offset = options.offset || this.offset;
        this.orderBy = options.orderBy || this.orderBy;
        this.order = options.order || this.order;
        this.where = options.where || this.where;

        const length = this.length + this.lengthCorrection;

        if (!('maxSize' in options)) {
            options.data.maxSize = options.more ? this.maxSize : (
                (length > this.maxSize) ? length : this.maxSize
            );

            if (this.maxMaxSize && options.data.maxSize > this.maxMaxSize) {
                options.data.maxSize = this.maxMaxSize;
            }
        }
        else {
            options.data.maxSize = options.maxSize;
        }

        options.data.offset = options.more ? length : this.offset;
        options.data.orderBy = this.orderBy;
        options.data.order = this.order;
        options.data.where = this.getWhere();

        options = {prepare: true, ...options};

        const success = options.success;

        options.success = response => {
            options.reset ?
                this.reset(response, options) :
                this.set(response, options);

            if (success) {
                success.call(options.context, this, response, options);
            }

            this.trigger('sync', this, response, options);
        };

        const error = options.error;

        options.error = response => {
            if (error) {
                error.call(options.context, this, response, options);
            }

            this.trigger('error', this, response, options);
        };

        this.lastSyncPromise = Model.prototype.sync.call(this, 'read', this, options);

        return this.lastSyncPromise;
    }

    
    abortLastFetch() {
        if (this.lastSyncPromise && this.lastSyncPromise.getReadyState() < 4) {
            this.lastSyncPromise.abort();
        }
    }

    
    getWhere() {
        let where = (this.where || []).concat(this.whereAdditional || []);

        if (this.whereFunction) {
            where = where.concat(this.whereFunction() || []);
        }

        return where;
    }

    
    getEntityType() {
        return this.entityType || this.name;
    }

    
    resetOrderToDefault() {
        this.orderBy = this.defaultOrderBy;
        this.order = this.defaultOrder;
    }

    
    setOrder(orderBy, order, setDefault) {
        this.orderBy = orderBy;
        this.order = order;

        if (setDefault) {
            this.defaultOrderBy = orderBy;
            this.defaultOrder = order;
        }
    }

    
    clone() {
        const collection = new this.constructor(this.models, {
            model: this.model,
            entityType: this.entityType,
            defs: this.defs,
            orderBy: this.orderBy,
            order: this.order,
        });

        collection.name = this.name;
        collection.urlRoot = this.urlRoot;
        collection.url = this.url;
        collection.defaultOrder = this.defaultOrder;
        collection.defaultOrderBy = this.defaultOrderBy;
        collection.data = Espo.Utils.cloneDeep(this.data);
        collection.where = Espo.Utils.cloneDeep(this.where);
        collection.whereAdditional = Espo.Utils.cloneDeep(this.whereAdditional);
        collection.total = this.total;
        collection.offset = this.offset;
        collection.maxSize = this.maxSize;
        collection.maxMaxSize = this.maxMaxSize;
        collection.whereFunction = this.whereFunction;

        return collection;
    }

    
    prepareModel() {
        return this._prepareModel({});
    }

    
    
    composeSyncUrl() {
        return this.url;
    }

    
    _isModel(object) {
        return object instanceof Model;
    }

    
    _removeModels(models, options) {
        const removed = [];

        for (let i = 0; i < models.length; i++) {
            const model = this.get(models[i]);

            if (!model) {
                continue;
            }

            const index = this.models.indexOf(model);

            this.models.splice(index, 1);
            this.length--;

            delete this._byId[model.cid];
            const id = this.modelId(model.attributes);

            if (id != null) {
                delete this._byId[id];
            }

            if (!options.silent) {
                options.index = index;

                model.trigger('remove', model, this, options);
            }

            removed.push(model);

            this._removeReference(model, options);
        }

        return removed;
    }

    
    _addReference(model) {
        this._byId[model.cid] = model;

        const id = this.modelId(model.attributes);

        if (id != null) {
            this._byId[id] = model;
        }

        model.on('all', this._onModelEvent, this);
    }

    
    _removeReference(model) {
        delete this._byId[model.cid];

        const id = this.modelId(model.attributes);

        if (id != null) {
            delete this._byId[id];
        }

        if (this === model.collection) {
            delete model.collection;
        }

        model.off('all', this._onModelEvent, this);
    }

    
    _onModelEvent(event, model, collection, options) {
        if (event === 'sync' && collection !== this) {
            return;
        }

        if (!model) {
            this.trigger.apply(this, arguments);

            return;
        }

        if ((event === 'add' || event === 'remove') && collection !== this) {
            return;
        }

        if (event === 'destroy') {
            this.remove(model, options);
        }

        if (event === 'change') {
            const prevId = this.modelId(model.previousAttributes());
            const id = this.modelId(model.attributes);

            if (prevId !== id) {
                if (prevId != null) {
                    delete this._byId[prevId];
                }

                if (id != null) {
                    this._byId[id] = model;
                }
            }
        }

        this.trigger.apply(this, arguments);
    }

    
    
    _prepareModel(attributes) {
        if (this._isModel(attributes)) {
            if (!attributes.collection) {
                attributes.collection = this;
            }

            return attributes;
        }

        const ModelClass = this.model;

        
        return new ModelClass(attributes, {
            collection: this,
            entityType: this.entityType || this.name,
            defs: this.defs,
        });
    }
}

Object.assign(Collection.prototype, Events);

Collection.extend = BullView.extend;

const setOptions = {
    add: true,
    remove: true,
    merge: true,
};

const addOptions = {
    add: true,
    remove: false,
};

const splice = (array, insert, at) => {
    at = Math.min(Math.max(at, 0), array.length);

    const tail = Array(array.length - at);
    const length = insert.length;
    let i;

    for (i = 0; i < tail.length; i++) {
        tail[i] = array[i + at];
    }

    for (i = 0; i < length; i++) {
        array[i + at] = insert[i];
    }

    for (i = 0; i < tail.length; i++) {
        array[i + length + at] = tail[i];
    }
};

export default Collection;
