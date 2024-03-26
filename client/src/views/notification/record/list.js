



import ListExpandedRecordView from 'views/record/list-expanded';

class NotificationListRecordView extends ListExpandedRecordView {

    

    setup() {
        super.setup();

        this.listenTo(this.collection, 'sync', (c, r, options) => {
            if (!options.fetchNew) {
                return;
            }

            let lengthBeforeFetch = options.lengthBeforeFetch || 0;

            if (lengthBeforeFetch === 0) {
                this.reRender();

                return;
            }

            let $list = this.$el.find(this.listContainerEl);

            let rowCount = this.collection.length - lengthBeforeFetch;

            for (let i = rowCount - 1; i >= 0; i--) {
                let model = this.collection.at(i);

                $list.prepend(
                    $(this.getRowContainerHtml(model.id))
                );

                this.buildRow(i, model, view => {
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

    showNewRecords() {
        this.collection.fetchNew();
    }
}

export default NotificationListRecordView;
