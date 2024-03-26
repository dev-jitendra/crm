

import BaseDashletView from 'views/dashlets/abstract/base';

class StreamDashletView extends BaseDashletView {

    name = 'Stream'

    templateContent = '<div class="list-container">{{{list}}}</div>'

    actionRefresh() {
        if (!this.getRecordView()) {
            return;
        }

        this.getRecordView().showNewRecords();
    }

    afterRender() {
        this.getCollectionFactory().create('Note', collection => {
            this.collection = collection;

            collection.url = 'Stream';
            collection.maxSize = this.getOption('displayRecords');

            if (this.getOption('skipOwn')) {
                collection.data.skipOwn = true;
            }

            collection.fetch()
                .then(() => {
                    this.createView('list', 'views/stream/record/list', {
                        selector: '> .list-container',
                        collection: collection,
                        isUserStream: true,
                        noEdit: false,
                    }, view => {
                        view.render();
                    });
                })
        });
    }

    
    getRecordView() {
        return this.getView('list');
    }

    setupActionList() {
        this.actionList.unshift({
            name: 'viewList',
            text: this.translate('View'),
            iconHtml: '<span class="fas fa-align-justify"></span>',
            url: '#Stream',
        });

        if (!this.getUser().isPortal()) {
            this.actionList.unshift({
                name: 'create',
                text: this.translate('Create Post', 'labels'),
                iconHtml: '<span class="fas fa-plus"></span>',
            });
        }
    }

    
    actionCreate() {
        this.createView('dialog', 'views/stream/modals/create-post', {}, view => {
            view.render();

            this.listenToOnce(view, 'after:save', () => {
                view.close();

                this.actionRefresh();
            });
        });
    }

    
    actionViewList() {
        this.getRouter().navigate('#Stream', {trigger: true});
    }
}

export default StreamDashletView;
