



import Controller from 'controller';


class RecordController extends Controller {

    
    defaultAction = 'list'

    constructor(params, injections) {
        super(params, injections);

        
        this.collectionMap = {};
    }

    
    checkAccess(action) {
        if (this.getAcl().check(this.name, action)) {
            return true;
        }

        return false;
    }

    
    getViewName(type) {
        return this.getMetadata().get(['clientDefs', this.name, 'views', type]) ||
            'views/' + Espo.Utils.camelCaseToHyphen(type);
    }

    
    beforeList() {
        this.handleCheckAccess('read');
    }

    actionList(options) {
        const isReturn = options.isReturn || this.getRouter().backProcessed;

        const key = this.name + 'List';

        if (!isReturn && this.getStoredMainView(key)) {
            this.clearStoredMainView(key);
        }

        this.getCollection().then(collection => {
            const mediator = {};

            const abort = () => {
                collection.abortLastFetch();
                mediator.abort = true;

                Espo.Ui.notify(false);
            };

            this.listenToOnce(this.baseController, 'action', abort);
            this.listenToOnce(collection, 'sync', () => this.stopListening(this.baseController, 'action', abort));

            const viewOptions = {
                scope: this.name,
                collection: collection,
                params: options,
                mediator: mediator,
            };

            const viewName = this.getViewName('list');

            const params = {
                useStored: isReturn,
                key: key,
            };

            this.main(viewName, viewOptions, null, params);
        });
    }

    beforeView() {
        this.handleCheckAccess('read');
    }

    
    createViewView(options, model, view) {
        view = view || this.getViewName('detail');

        this.main(view, {
            scope: this.name,
            model: model,
            returnUrl: options.returnUrl,
            returnDispatchParams: options.returnDispatchParams,
            params: options,
        });
    }

    
    prepareModelView(model, options) {}

    
    
    actionView(options) {
        const id = options.id;

        const isReturn = this.getRouter().backProcessed;

        if (isReturn) {
            if (this.lastViewActionOptions && this.lastViewActionOptions.id === id) {
                options = Espo.Utils.clone(this.lastViewActionOptions);

                if (options.model && options.model.get('deleted')) {
                    delete options.model;
                }
            }

            options.isReturn = true;
        }
        else {
            delete this.lastViewActionOptions;
        }

        this.lastViewActionOptions = options;

        const createView = model => {
            this.prepareModelView(model, options);

            this.createViewView.call(this, options, model);
        };

        if ('model' in options) {
            const model = options.model;

            createView(model);

            this.showLoadingNotification();

            model.fetch()
                .then(() => this.hideLoadingNotification())
                .catch(xhr => {
                    if (
                        xhr.status === 403 &&
                        options.isAfterCreate
                    ) {
                        this.hideLoadingNotification();
                        xhr.errorIsHandled = true;

                        model.trigger('fetch-forbidden');
                    }
                });

            this.listenToOnce(this.baseController, 'action', () => {
                model.abortLastFetch();
                this.hideLoadingNotification();
            });

            return;
        }

        this.getModel().then(model => {
            model.id = id;

            this.showLoadingNotification();

            model.fetch({main: true})
                .then(() => {
                    this.hideLoadingNotification();

                    if (model.get('deleted')) {
                        this.listenToOnce(model, 'after:restore-deleted', () => {
                            createView(model);
                        });

                        this.prepareModelView(model, options);
                        this.createViewView(options, model, 'views/deleted-detail');

                        return;
                    }

                    createView(model);
                });

            this.listenToOnce(this.baseController, 'action', () => {
                model.abortLastFetch();
            });
        });
    }

    
    beforeCreate() {
        this.handleCheckAccess('create');
    }

    
    
    prepareModelCreate(model, options) {
        this.listenToOnce(model, 'before:save', () => {
            const key = this.name + 'List';

            const stored = this.getStoredMainView(key);

            if (!stored) {
                return;
            }

            if (!('storeViewAfterCreate' in stored) || !stored.storeViewAfterCreate) {
                this.clearStoredMainView(key);
            }
        });

        this.listenToOnce(model, 'after:save', () => {
            const key = this.name + 'List';

            const stored = this.getStoredMainView(key);

            if (!stored) {
                return;
            }

            if (!('storeViewAfterCreate' in stored) || !stored.storeViewAfterCreate) {
                return;
            }

            if (!('collection' in stored) || !stored.collection) {
                return;
            }

            this.listenToOnce(stored, 'after:render', () => stored.collection.fetch());
        });
    }

    create(options) {
        options = options || {};

        const optionsOptions = options.options || {};

        this.getModel().then(model => {
            if (options.relate) {
                model.setRelate(options.relate);
            }

            const o = {
                scope: this.name,
                model: model,
                returnUrl: options.returnUrl,
                returnDispatchParams: options.returnDispatchParams,
                params: options,
            };

            for (const k in optionsOptions) {
                o[k] = optionsOptions[k];
            }

            if (options.attributes) {
                model.set(options.attributes);
            }

            this.prepareModelCreate(model, options);

            this.main(this.getViewName('edit'), o);
        });
    }

    actionCreate(options) {
        this.create(options);
    }

    
    beforeEdit() {
        this.handleCheckAccess('edit');
    }

    
    
    prepareModelEdit(model, options) {
        this.listenToOnce(model, 'before:save', () => {
            const key = this.name + 'List';

            const stored = this.getStoredMainView(key);

            if (!stored) {
                return;
            }

            if (!('storeViewAfterUpdate' in stored) || !stored.storeViewAfterUpdate) {
                this.clearStoredMainView(key);
            }
        });
    }

    actionEdit(options) {
        const id = options.id;

        const optionsOptions = options.options || {};

        this.getModel().then(model => {
            model.id = id;

            if (options.model) {
                model = options.model;
            }

            this.prepareModelEdit(model, options);

            this.showLoadingNotification();

            model
                .fetch({main: true})
                .then(() => {
                    this.hideLoadingNotification();

                    const o = {
                        scope: this.name,
                        model: model,
                        returnUrl: options.returnUrl,
                        returnDispatchParams: options.returnDispatchParams,
                        params: options,
                    };

                    for (const k in optionsOptions) {
                        o[k] = optionsOptions[k];
                    }

                    if (options.attributes) {
                        o.attributes = options.attributes;
                    }

                    this.main(this.getViewName('edit'), o);
                });

            this.listenToOnce(this.baseController, 'action', () => {
                model.abortLastFetch();
            });
        });
    }

    
    beforeMerge() {
        this.handleCheckAccess('edit');
    }

    
    actionMerge(options) {
        const ids = options.ids.split(',');

        this.getModel().then((model) => {
            const models = [];

            const proceed = () => {
                this.main('views/merge', {
                    models: models,
                    scope: this.name,
                    collection: options.collection
                });
            };

            let i = 0;

            ids.forEach(id => {
                const current = model.clone();

                current.id = id;
                models.push(current);

                this.listenToOnce(current, 'sync', () => {
                    i++;

                    if (i === ids.length) {
                        proceed();
                    }
                });

                current.fetch();
            });
        });
    }

    
    actionRelated(options) {
        const id = options.id;
        const link = options.link;

        const viewName = this.getViewName('listRelated');

        let model;

        this.getModel()
            .then(m => {
                model = m;
                model.id = id;

                return model.fetch({main: true});
            })
            .then(() => {
                const foreignEntityType = model.getLinkParam(link, 'entity');

                if (!foreignEntityType) {
                    this.baseController.error404();

                    throw new Error(`Bad link '${link}'.`);
                }

                return this.collectionFactory.create(foreignEntityType);
            })
            .then(collection => {
                collection.url = model.entityType + '/' + id + '/' + link;

                this.main(viewName, {
                    scope: this.name,
                    model: model,
                    collection: collection,
                    link: link,
                });
            })
    }

    
    getCollection(usePreviouslyFetched) {
        if (!this.name) {
            throw new Error('No collection for unnamed controller');
        }

        const entityType = this.entityType || this.name;

        if (usePreviouslyFetched && entityType in this.collectionMap) {
            const collection = this.collectionMap[entityType];

            return Promise.resolve(collection);
        }

        return this.collectionFactory.create(entityType, collection => {
            this.collectionMap[entityType] = collection;

            this.listenTo(collection, 'sync', () => collection.isFetched = true);
        });
    }

    
    getModel(callback, context) {
        context = context || this;

        if (!this.name) {
            throw new Error('No collection for unnamed controller');
        }

        const modelName = this.entityType || this.name;

        return this.modelFactory.create(modelName, model => {
            if (callback) {
                callback.call(context, model);
            }
        });
    }
}

export default RecordController;
