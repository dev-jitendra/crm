



class ActionItemSetupHelper {
    
    constructor(metadata, viewHelper, acl, language) {
        this.metadata = metadata;
        this.viewHelper = viewHelper;
        this.acl = acl;
        this.language = language;
    }

    
    setup(view, type, waitFunc, addFunc, showFunc, hideFunc, options) {
        options = options || {};
        const actionList = [];

        
        const scope = view.scope || view.model.entityType;

        if (!scope) {
            throw new Error();
        }

        const actionDefsList = [
            ...this.metadata.get(['clientDefs', 'Global', type + 'ActionList']) || [],
            ...this.metadata.get(['clientDefs', scope, type + 'ActionList']) || [],
        ];

        actionDefsList.forEach(item => {
            if (typeof item === 'string') {
                item = {name: item};
            }

            item = Espo.Utils.cloneDeep(item);

            const name = item.name;

            if (!item.label) {
                item.html = this.language.translate(name, 'actions', scope);
            }

            item.data = item.data || {};

            const handlerName = item.handler || item.data.handler;

            if (handlerName && !item.data.handler) {
                item.data.handler = handlerName;
            }

            addFunc(item);

            if (!Espo.Utils.checkActionAvailability(this.viewHelper, item)) {
                return;
            }

            if (!Espo.Utils.checkActionAccess(this.acl, view.model, item, true)) {
                item.hidden = true;
            }

            actionList.push(item);

            if (!handlerName) {
                return;
            }

            if (!item.initFunction && !item.checkVisibilityFunction) {
                return;
            }

            waitFunc(new Promise(resolve => {
                Espo.loader.require(handlerName, Handler => {
                    const handler = new Handler(view);

                    if (item.initFunction) {
                        handler[item.initFunction].call(handler);
                    }

                    if (item.checkVisibilityFunction) {
                        const isNotVisible = !handler[item.checkVisibilityFunction].call(handler);

                        if (isNotVisible) {
                            hideFunc(item.name);
                        }
                    }

                    item.handlerInstance = handler;

                    resolve();
                });
            }));
        });

        if (!actionList.length) {
            return;
        }

        const onSync = () => {
            actionList.forEach(item => {
                if (item.handlerInstance && item.checkVisibilityFunction) {
                    const isNotVisible = !item.handlerInstance[item.checkVisibilityFunction]
                        .call(item.handlerInstance);

                    if (isNotVisible) {
                        hideFunc(item.name);

                        return;
                    }
                }

                if (Espo.Utils.checkActionAccess(this.acl, view.model, item, true)) {
                    showFunc(item.name);

                    return;
                }

                hideFunc(item.name);
            });
        };

        if (options.listenToViewModelSync) {
            view.listenTo(view, 'model-sync', () => onSync());

            return;
        }

        view.listenTo(view.model, 'sync', () => onSync());
    }
}

export default ActionItemSetupHelper;
