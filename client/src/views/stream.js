

import View from 'view';

class StreamView extends View {

    template ='stream'
    filterList = ['all', 'posts', 'updates']
    filter = false

    events = {
        
        'click button[data-action="refresh"]': function () {
            if (!this.getRecordView()) {
                return;
            }

            this.getRecordView().showNewRecords();
        },
        
        'click button[data-action="selectFilter"]': function (e) {
            const data = $(e.currentTarget).data();

            this.actionSelectFilter(data);
        },
    }

    data() {
        let filter = this.filter;

        if (filter === false) {
            filter = 'all';
        }

        return {
            displayTitle: this.options.displayTitle,
            filterList: this.filterList,
            filter: filter,
        };
    }

    setup() {
        this.filter = this.options.filter || this.filter;

        this.wait(
            this.getModelFactory().create('Note', model => {
                this.createView('createPost', 'views/stream/record/edit', {
                    selector: '.create-post-container',
                    model: model,
                    interactiveMode: true,
                }, view => {
                    this.listenTo(view, 'after:save', () => this.getRecordView().showNewRecords());
                });
            })
        );
    }

    afterRender() {
        Espo.Ui.notify(' ... ');

        this.getCollectionFactory().create('Note', collection => {
            this.collection = collection;
            collection.url = 'Stream';

            this.setFilter(this.filter);

            collection.fetch().then(() => {
                this.createView('list', 'views/stream/record/list', {
                    selector: '.list-container',
                    collection: collection,
                    isUserStream: true,
                }, view => {
                    view.notify(false);

                    view.render()
                        .then(view => {
                            view.$el.find('> .list > .list-group');
                        });
                });
            });
        });
    }

    
    getRecordView() {
        return this.getView('list');
    }

    actionSelectFilter(data) {
        const name = data.name;
        const filter = name;

        let internalFilter = name;

        if (filter === 'all') {
            internalFilter = false;
        }

        this.filter = internalFilter;
        this.setFilter(this.filter);

        this.filterList.forEach((item) => {
            const $el = this.$el.find('.page-header button[data-action="selectFilter"][data-name="' + item + '"]');

            if (item === filter) {
                $el.addClass('active');
            } else {
                $el.removeClass('active');
            }
        });

        let url = '#Stream';

        if (this.filter) {
            url += '/' + filter;
        }

        this.getRouter().navigate(url);

        Espo.Ui.notify(' ... ');

        this.listenToOnce(this.collection, 'sync', () => {
            Espo.Ui.notify(false);
        });

        this.collection.reset();
        this.collection.fetch();
    }

    setFilter(filter) {
        this.collection.data.filter = null;

        if (filter) {
            this.collection.data.filter = filter;
        }

        this.collection.offset = 0;
        this.collection.maxSize = this.getConfig().get('recordsPerPage') || this.collection.maxSize;
    }
}

export default StreamView;
