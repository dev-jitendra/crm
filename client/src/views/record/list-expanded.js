



import ListRecordView from 'views/record/list';

class ListExpandedRecordView extends ListRecordView {

    template = 'record/list-expanded'

    checkboxes = false
    selectable = false
    rowActionsView = false
    _internalLayoutType = 'list-row-expanded'
    presentationType = 'expanded'
    pagination = false
    header = false
    _internalLayout = null
    checkedList = null
    listContainerEl = '.list > ul'

    setup() {
        super.setup();

        this.on('after:save', model => {
            const view = this.getView(model.id);

            if (!view) {
                return;
            }

            view.reRender();
        });

        
        this.displayTotalCount = false;
    }

    _loadListLayout(callback) {
        const type = this.type + 'Expanded';

        this.layoutLoadCallbackList.push(callback);

        if (this.layoutIsBeingLoaded) {
            return;
        }

        this.layoutIsBeingLoaded = true;

        this._helper.layoutManager.get(this.collection.entityType, type, listLayout => {
            this.layoutLoadCallbackList.forEach(c => {
                c(listLayout);

                this.layoutLoadCallbackList = [];
                this.layoutIsBeingLoaded = false;
            });
        });
    }

    _convertLayout(listLayout, model) {
        model = model || this.collection.prepareModel();

        const layout = {
            rows: [],
            right: false,
        };

        for (const i in listLayout.rows) {
            const row = listLayout.rows[i];
            const layoutRow = [];

            for (const j in row) {
                const rowItem = row[j];
                const type = rowItem.type || model.getFieldType(rowItem.name) || 'base';

                const item = {
                    name: rowItem.name + 'Field',
                    field: rowItem.name,
                    view: rowItem.view ||
                        model.getFieldParam(rowItem.name, 'view') ||
                        this.getFieldManager().getViewName(type),
                    options: {
                        defs: {
                            name: rowItem.name,
                            params: rowItem.params || {}
                        },
                        mode: 'list',
                    },
                };

                if (rowItem.options) {
                    for (const optionName in rowItem.options) {
                        if (typeof item.options[optionName] !== 'undefined') {
                            continue;
                        }

                        item.options[optionName] = rowItem.options[optionName];
                    }
                }

                if (rowItem.link) {
                    item.options.mode = 'listLink';
                }

                layoutRow.push(item);
            }

            layout.rows.push(layoutRow);
        }

        if ('right' in listLayout) {
            if (listLayout.right) {
                const name = listLayout.right.name || 'right';

                layout.right = {
                    field: name,
                    name: name,
                    view: listLayout.right.view,
                    options: {
                        defs: {
                            params: {
                                width: listLayout.right.width || '7%',
                            }
                        }
                    },
                };
            }
        }
        else {
            if (this.rowActionsView) {
                layout.right = this.getRowActionsDefs();
            }
        }

        return layout;
    }

    getRowSelector(id) {
        return 'li[data-id="' + id + '"]';
    }

    getItemEl(model, item) {
        const name = item.field || item.columnName;

        return this.getSelector() + ' li[data-id="' + model.id + '"] .cell[data-name="' + name+ '"]';
    }

    getRowContainerHtml(id) {
        return $('<li>')
            .attr('data-id', id)
            .addClass('list-group-item list-row')
            .get(0).outerHTML;
    }

    prepareInternalLayout(internalLayout, model) {
        const rows = internalLayout.rows || [];

        rows.forEach((row) => {
            row.forEach((col) => {
                col.el = this.getItemEl(model, col);
            });
        });

        if (internalLayout.right) {
            internalLayout.right.el = this.getItemEl(model, internalLayout.right);
        }
    }

    fetchAttributeListFromLayout() {
        const list = [];

        if (this.listLayout.rows) {
            this.listLayout.rows.forEach((row) => {
                row.forEach(item => {
                    if (!item.name) {
                        return;
                    }

                    const field = item.name;

                    const fieldType = this.getMetadata().get(['entityDefs', this.scope, 'fields', field, 'type']);

                    if (!fieldType) {
                        return;
                    }

                    this.getFieldManager()
                        .getEntityTypeFieldAttributeList(this.scope, field)
                        .forEach((attribute) => {
                            list.push(attribute);
                        });
                });
            });
        }

        return list;
    }
}

export default ListExpandedRecordView;
