



import ListRecordView from 'views/record/list';

class ListTreeRecordView extends ListRecordView {

    template = 'record/list-tree'

    showMore = false
    showCount = false
    checkboxes = false
    rowActionsView = false
    presentationType = 'tree'
    header = false
    listContainerEl = ' > .list > ul'
    checkAllResultDisabled = true
    showRoot = false
    massActionList = ['remove']
    selectable = false
    createDisabled = false
    selectedData = null
    level = 0
    itemViewName = 'views/record/list-tree-item'

    
    data() {
        const data = super.data();

        data.createDisabled = this.createDisabled;

        data.showRoot = this.showRoot;

        if (data.showRoot) {
            data.rootName = this.rootName || this.translate('Root');
        }

        data.showEditLink = this.showEditLink;

        if (this.level === 0 && this.selectable && (this.selectedData || {}).id === null) {
            data.rootIsSelected = true;
        }

        if (this.level === 0 && this.options.hasExpandedToggler) {
            data.hasExpandedToggler = true;
        }

        if (this.level === 0) {
            data.isExpanded = this.isExpanded;
        }

        if (data.hasExpandedToggler || this.showEditLink) {
            data.showRootMenu = true;
        }

        if (this.options.menuDisabled) {
            data.showRootMenu = false;
        }

        data.noData = data.createDisabled && !data.rowList.length && !data.showRoot;

        return data;
    }

    setup() {
        if ('selectable' in this.options) {
            this.selectable = this.options.selectable;
        }

        this.readOnly = this.options.readOnly;
        this.createDisabled = this.readOnly || this.options.createDisabled || this.createDisabled;
        this.isExpanded = this.options.isExpanded;

        if ('showRoot' in this.options) {
            this.showRoot = this.options.showRoot;

            if ('rootName' in this.options) {
                this.rootName = this.options.rootName;
            }
        }

        if ('showRoot' in this.options) {
            this.showEditLink = this.options.showEditLink;
        }

        if ('level' in this.options) {
            this.level = this.options.level;
        }

        this.rootView = this.options.rootView || this;

        if (this.level === 0) {
            this.selectedData = {
                id: null,
                path: [],
                names: {},
            };
        }

        if ('selectedData' in this.options) {
            this.selectedData = this.options.selectedData;
        }

        super.setup();

        if (this.selectable) {
            this.on('select', o => {
                if (o.id) {
                    this.$el.find('a.link[data-id="'+o.id+'"]').addClass('text-bold');

                    if (this.level === 0) {
                        this.$el.find('a.link').removeClass('text-bold');
                        this.$el.find('a.link[data-id="'+o.id+'"]').addClass('text-bold');

                        this.setSelected(o.id);

                        o.selectedData = this.selectedData;
                    }
                }

                if (this.level > 0) {
                    this.getParentView().trigger('select', o);
                }
            });
        }
    }

    
    setSelected(id) {
        if (id === null) {
            this.selectedData.id = null;
        }
        else {
            this.selectedData.id = id;
        }

        this.rowList.forEach(key => {
            const view = this.getView(key);

            if (view.model.id === id) {
                view.setIsSelected();
            }
            else {
                view.isSelected = false;
            }

            if (view.hasView('children')) {
                view.getChildrenView().setSelected(id);
            }
        });
    }

    buildRows(callback) {
        this.checkedList = [];
        this.rowList = [];

        if (this.collection.length > 0) {
            this.wait(true);

            const modelList = this.collection.models;
            const count = modelList.length;
            let built = 0;

            modelList.forEach(model => {
                const key = model.id;

                this.rowList.push(key);

                this.createView(key, this.itemViewName, {
                    model: model,
                    collection: this.collection,
                    selector: this.getRowSelector(model.id),
                    createDisabled: this.createDisabled,
                    readOnly: this.readOnly,
                    level: this.level,
                    isSelected: model.id === this.selectedData.id,
                    selectedData: this.selectedData,
                    selectable: this.selectable,
                    setViewBeforeCallback: this.options.skipBuildRows && !this.isRendered(),
                    rootView: this.rootView,
                }, () => {
                    built++;

                    if (built === count) {
                        if (typeof callback === 'function') {
                            callback();
                        }

                        this.wait(false);
                    }
                });
            });

            return;
        }

        if (typeof callback === 'function') {
            callback();
        }
    }

    getRowSelector(id) {
        return 'li[data-id="' + id + '"]';
    }

    getItemEl(model, item) {
        return this.getSelector() +
            ' li[data-id="' + model.id + '"] span.cell[data-name="' + item.name + '"]';
    }

    getCreateAttributes() {
        return {};
    }

    
    actionCreate(data, e) {
        e.stopPropagation();

        const attributes = this.getCreateAttributes();

        let maxOrder = 0;

        this.collection.models.forEach(m => {
            if (m.get('order') > maxOrder) {
                maxOrder = m.get('order');
            }
        });

        attributes.order = maxOrder + 1;

        attributes.parentId = null;
        attributes.parentName = null;

        if (this.model) {
            attributes.parentId = this.model.id;
            attributes.parentName = this.model.get('name');
        }

        const scope = this.collection.entityType;

        const viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.edit') ||
            'views/modals/edit';

        this.createView('quickCreate', viewName, {
            scope: scope,
            attributes: attributes,
        }, view => {
            view.render();

            this.listenToOnce(view, 'after:save', model => {
                view.close();

                const collection =  this.collection;

                model.set('childCollection', collection.createSeed());

                if (model.get('parentId') !== attributes.parentId) {
                    let v = this;

                    while (1) {
                        if (v.level) {
                            v = v.getParentView().getParentView();
                        }
                        else {
                            break;
                        }
                    }

                    v.collection.fetch();

                    return;
                }

                this.collection.push(model);

                this.buildRows(() => {
                    this.render();
                });
            });
        });
    }

    
    actionSelectRoot() {
        this.trigger('select', {id: null});

        if (this.selectable) {
            this.$el.find('a.link').removeClass('text-bold');
            this.$el.find('a.link[data-action="selectRoot"]').addClass('text-bold');

            this.setSelected(null);
        }
    }
}

export default ListTreeRecordView;
