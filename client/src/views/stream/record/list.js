



import ListExpandedRecordView from 'views/record/list-expanded';



class ListStreamRecordView extends ListExpandedRecordView {

    type = 'listStream'

    massActionsDisabled = true

    setup() {
        this.itemViews = this.getMetadata().get('clientDefs.Note.itemViews') || {};

        super.setup();

        this.isRenderingNew = false;

        this.listenTo(this.collection, 'sync', (c, r, options) => {
            if (!options.fetchNew) {
                return;
            }

            if (this.isRenderingNew) {
                
                return;
            }

            let lengthBeforeFetch = options.lengthBeforeFetch || 0;

            if (lengthBeforeFetch === 0) {
                this.buildRows(() => this.reRender());

                return;
            }

            let $list = this.$el.find(this.listContainerEl);

            let rowCount = this.collection.length - lengthBeforeFetch;

            if (rowCount === 0) {
                return;
            }

            this.isRenderingNew = true;

            for (let i = rowCount - 1; i >= 0; i--) {
                let model = this.collection.at(i);

                this.buildRow(i, model, view => {
                    if (i === 0) {
                        this.isRenderingNew = false;
                    }

                    let $row = $(this.getRowContainerHtml(model.id));

                    
                    let $existingRow = this.$el.find(`[data-id="${model.id}"]`);

                    if ($existingRow.length) {
                        $row = $existingRow;
                    }

                    if (!$existingRow.length) {
                        $list.prepend($row);
                    }

                    view.render();
                });
            }
        });

        this.events['auxclick a[href][data-scope][data-id]'] = e => {
            let isCombination = e.button === 1 && (e.ctrlKey || e.metaKey);

            if (!isCombination) {
                return;
            }

            let $target = $(e.currentTarget);

            let id = $target.attr('data-id');
            let scope = $target.attr('data-scope');

            e.preventDefault();
            e.stopPropagation();

            this.actionQuickView({
                id: id,
                scope: scope,
            });
        };
    }

    buildRow(i, model, callback) {
        let key = model.id;

        this.rowList.push(key);

        let type = model.get('type');
        let viewName = this.itemViews[type] || 'views/stream/notes/' + Espo.Utils.camelCaseToHyphen(type);

        this.createView(key, viewName, {
            model: model,
            parentModel: this.model,
            acl: {
                edit: this.getAcl().checkModel(model, 'edit')
            },
            isUserStream: this.options.isUserStream,
            noEdit: this.options.noEdit,
            optionsToPass: ['acl'],
            name: this.type + '-' + model.entityType,
            selector: 'li[data-id="' + model.id + '"]',
            setViewBeforeCallback: this.options.skipBuildRows && !this.isRendered(),
        }, callback);
    }

    buildRows(callback) {
        this.checkedList = [];
        this.rowList = [];

        if (this.collection.length > 0) {
            this.wait(true);

            let count = this.collection.models.length;
            let built = 0;

            for (let i in this.collection.models) {
                let model = this.collection.models[i];

                this.buildRow(i, model, () => {
                    built++;

                    if (built === count) {
                        if (typeof callback === 'function') {
                            callback();
                        }

                        this.wait(false);

                        this.trigger('after:build-rows');
                    }
                });
            }

            return;
        }

        if (typeof callback === 'function') {
            callback();

            this.trigger('after:build-rows');
        }
    }

    showNewRecords() {
        this.collection.fetchNew();
    }
}

export default ListStreamRecordView;
