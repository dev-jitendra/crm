



import View from 'view';


class MainView extends View {

    
    scope = ''

    
    name = ''

    

    
    menu = {}

    
    $headerActionsContainer = null

    
    shortcutKeys = null

    
    events = {
        
        'click .action': function (e) {
            Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget, {
                actionItems: [...this.menu.buttons, ...this.menu.dropdown],
                className: 'main-header-manu-action',
            });
        },
    }

    lastUrl

    
    init() {
        this.scope = this.options.scope || this.scope;
        this.menu = {};

        this.options.params = this.options.params || {};

        if (this.name && this.scope) {
            const key = this.name.charAt(0).toLowerCase() + this.name.slice(1);

            this.menu = this.getMetadata().get(['clientDefs', this.scope, 'menu', key]) || {};
        }

        
        this.headerActionItemTypeList = ['buttons', 'dropdown', 'actions'];

        this.menu = Espo.Utils.cloneDeep(this.menu);

        let globalMenu = {};

        if (this.name) {
            globalMenu = Espo.Utils.cloneDeep(
                this.getMetadata()
                    .get(['clientDefs', 'Global', 'menu',
                        this.name.charAt(0).toLowerCase() + this.name.slice(1)]) || {}
            );
        }

        this._reRenderHeaderOnSync = false;

        this._menuHandlers = {};

        this.headerActionItemTypeList.forEach(type => {
            this.menu[type] = this.menu[type] || [];
            this.menu[type] = this.menu[type].concat(globalMenu[type] || []);

            const itemList = this.menu[type];

            itemList.forEach(item => {
                const viewObject = this;

                if (
                    (item.initFunction || item.checkVisibilityFunction) &&
                    (item.handler || item.data && item.data.handler)
                ) {
                    this.wait(new Promise(resolve => {
                        const handler = item.handler || item.data.handler;

                        Espo.loader.require(handler, Handler => {
                            const handler = new Handler(viewObject);

                            const name = item.name || item.action;

                            if (name) {
                                this._menuHandlers[name] = handler;
                            }

                            if (item.initFunction) {
                                handler[item.initFunction].call(handler);
                            }

                            if (item.checkVisibilityFunction && this.model) {
                                this._reRenderHeaderOnSync = true;
                            }

                            resolve();
                        });
                    }));
                }
            });
        });

        if (this.model) {
            this.whenReady().then(() => {
                if (!this._reRenderHeaderOnSync) {
                    return;
                }

                this.listenTo(this.model, 'sync', () => {
                    if (!this.getHeaderView()) {
                        return;
                    }

                    this.getHeaderView().reRender();
                });
            });
        }

        this.updateLastUrl();

        this.on('after:render-internal', () => {
            this.$headerActionsContainer = this.$el.find('.page-header .header-buttons');
        });

        this.on('header-rendered', () => {
            this.$headerActionsContainer = this.$el.find('.page-header .header-buttons');

            this.adjustButtons();
        });

        this.on('after:render', () => this.adjustButtons());

        if (this.shortcutKeys) {
            this.shortcutKeys = Espo.Utils.cloneDeep(this.shortcutKeys);
        }
    }

    setupFinal() {
        if (this.shortcutKeys) {
            this.events['keydown.main'] = e => {
                const key = Espo.Utils.getKeyFromKeyEvent(e);

                if (typeof this.shortcutKeys[key] === 'function') {
                    this.shortcutKeys[key].call(this, e.originalEvent);

                    return;
                }

                const actionName = this.shortcutKeys[key];

                if (!actionName) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const methodName = 'action' + Espo.Utils.upperCaseFirst(actionName);

                if (typeof this[methodName] === 'function') {
                    this[methodName]();

                    return;
                }

                this[actionName]();
            };
        }
    }

    
    updateLastUrl() {
        this.lastUrl = this.getRouter().getCurrentUrl();
    }

    
    getMenu() {
        if (this.menuDisabled || !this.menu) {
            return {};
        }

        const menu = {};

        this.headerActionItemTypeList.forEach(type => {
            (this.menu[type] || []).forEach(item => {
                if (item === false) {
                    menu[type].push(false);

                    return;
                }

                item = Espo.Utils.clone(item);

                menu[type] = menu[type] || [];

                if (!Espo.Utils.checkActionAvailability(this.getHelper(), item)) {
                    return;
                }

                if (!Espo.Utils.checkActionAccess(this.getAcl(), this.model || this.scope, item)) {
                    return;
                }

                if (item.accessDataList) {
                    if (!Espo.Utils
                        .checkAccessDataList(item.accessDataList, this.getAcl(), this.getUser())
                    ) {
                        return;
                    }
                }

                item.name = item.name || item.action;
                item.action = item.action || null;

                if (this._menuHandlers[item.name] && item.checkVisibilityFunction) {
                    const handler = this._menuHandlers[item.name];

                    if (!handler[item.checkVisibilityFunction](item.name)) {
                        return;
                    }
                }

                if (item.labelTranslation) {
                    item.html = this.getHelper().escapeString(
                        this.getLanguage().translatePath(item.labelTranslation)
                    );
                }

                menu[type].push(item);
            });
        });

        return menu;
    }

    
    getHeader() {
        return '';
    }

    
    buildHeaderHtml(itemList) {
        const $itemList = itemList.map(item => {
            return $('<div>')
                .addClass('breadcrumb-item')
                .append(item);
        });

        const $div = $('<div>')
            .addClass('header-breadcrumbs');

        $itemList.forEach(($item, i) => {
            $div.append($item);

            if (i === $itemList.length - 1) {
                return;
            }

            $div.append(
                $('<div>')
                    .addClass('breadcrumb-separator')
                    .append(
                        $('<span>').addClass('chevron-right')
                    )
            )
        });

        return $div.get(0).outerHTML;
    }


    
    getHeaderIconHtml() {
        return this.getHelper().getScopeColorIconHtml(this.scope);
    }

    
    
    actionShowModal(data) {
        const view = data.view;

        if (!view) {
            return;
        }

        this.createView('modal', view, {
            model: this.model,
            collection: this.collection,
        }, view => {
            view.render();

            this.listenTo(view, 'after:save', () => {
                if (this.model) {
                    this.model.fetch();
                }

                if (this.collection) {
                    this.collection.fetch();
                }
            });
        });
    }

    
    addMenuItem(type, item, toBeginning, doNotReRender) {
        if (item) {
            item.name = item.name || item.action || Espo.Utils.generateId();

            const name = item.name;

            let index = -1;

            this.menu[type].forEach((data, i) => {
                data = data || {};

                if (data.name === name) {
                    index = i;
                }
            });

            if (~index) {
                this.menu[type].splice(index, 1);
            }
        }

        let method = 'push';

        if (toBeginning) {
            method  = 'unshift';
        }

        this.menu[type][method](item);

        if (!doNotReRender && this.isRendered()) {
            this.getHeaderView().reRender();

            return;
        }

        if (!doNotReRender && this.isBeingRendered()) {
            this.once('after:render', () => {
                this.getHeaderView().reRender();
            });
        }
    }

    
    removeMenuItem(name, doNotReRender) {
        let index = -1;
        let type = false;

        this.headerActionItemTypeList.forEach(t => {
            (this.menu[t] || []).forEach((item, i) => {
                item = item || {};

                if (item.name === name) {
                    index = i;
                    type = t;
                }
            });
        });

        if (~index && type) {
            this.menu[type].splice(index, 1);
        }

        if (!doNotReRender && this.isRendered()) {
            this.getHeaderView().reRender();

            return;
        }

        if (!doNotReRender && this.isBeingRendered()) {
            this.once('after:render', () => {
                this.getHeaderView().reRender();

            });

            return;
        }

        if (doNotReRender && this.isRendered()) {
            this.$headerActionsContainer.find('[data-name="' + name + '"]').remove();
        }
    }

    
    disableMenuItem(name) {
        if (!this.$headerActionsContainer) {
            return;
        }

        this.$headerActionsContainer
            .find('[data-name="' + name + '"]')
            .addClass('disabled')
            .attr('disabled');
    }

    
    enableMenuItem(name) {
        if (!this.$headerActionsContainer) {
            return;
        }

        this.$headerActionsContainer
            .find('[data-name="' + name + '"]')
            .removeClass('disabled')
            .removeAttr('disabled');
    }

    
    
    actionNavigateToRoot(data, event) {
        event.stopPropagation();

        this.getRouter().checkConfirmLeaveOut(() => {
            const options = {
                isReturn: true,
            };

            const rootUrl = this.options.rootUrl || this.options.params.rootUrl || '#' + this.scope;

            this.getRouter().navigate(rootUrl, {trigger: false});
            this.getRouter().dispatch(this.scope, null, options);
        });
    }

    
    hideHeaderActionItem(name) {
        this.headerActionItemTypeList.forEach(t => {
            (this.menu[t] || []).forEach(item => {
                item = item || {};

                if (item.name === name) {
                    item.hidden = true;
                }
            });
        });

        if (!this.isRendered()) {
            return;
        }

        this.$headerActionsContainer.find('li > .action[data-name="'+name+'"]').parent().addClass('hidden');
        this.$headerActionsContainer.find('a.action[data-name="'+name+'"]').addClass('hidden');

        this.controlMenuDropdownVisibility();
        this.adjustButtons();
    }

    
    showHeaderActionItem(name) {
        this.headerActionItemTypeList.forEach(t => {
            (this.menu[t] || []).forEach(item => {
                item = item || {};

                if (item.name === name) {
                    item.hidden = false;
                }
            });
        });

        const processUi = () => {
            this.$headerActionsContainer.find('li > .action[data-name="' + name + '"]').parent().removeClass('hidden');
            this.$headerActionsContainer.find('a.action[data-name="' + name + '"]').removeClass('hidden');

            this.controlMenuDropdownVisibility();
            this.adjustButtons();
        };

        if (!this.isRendered()) {
            if (this.isBeingRendered()) {
                this.whenRendered().then(() => processUi());
            }

            return;
        }

        processUi();
    }

    
    hasMenuVisibleDropdownItems() {
        let hasItems = false;

        (this.menu.dropdown || []).forEach(item => {
            if (!item.hidden) {
                hasItems = true;
            }
        });

        return hasItems;
    }

    
    controlMenuDropdownVisibility() {
        const $group = this.$headerActionsContainer.find('.dropdown-group');

        if (this.hasMenuVisibleDropdownItems()) {
            $group.removeClass('hidden');
            $group.find('> button').removeClass('hidden');

            return;
        }

        $group.addClass('hidden');
        $group.find('> button').addClass('hidden');
    }

    
    getHeaderView() {
        return this.getView('header');
    }

    
    adjustButtons() {
        const $buttons = this.$headerActionsContainer.find('.btn');

        $buttons
            .removeClass('radius-left')
            .removeClass('radius-right');

        const $buttonsVisible = $buttons.filter(':not(.hidden)');

        $buttonsVisible.first().addClass('radius-left');
        $buttonsVisible.last().addClass('radius-right');
    }

    
    setupReuse(params) {}
}

export default MainView;
