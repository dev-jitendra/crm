

import View from 'view';

class NotificationListView extends View {

    template = 'notification/list'

    setup() {
        this.addActionHandler('refresh', () => this.getRecordView().showNewRecords());
        this.addActionHandler('markAllNotificationsRead', () => this.actionMarkAllRead());

        const promise =
            this.getCollectionFactory().create('Notification')
                .then(collection => {
                    this.collection = collection;
                    this.collection.maxSize = this.getConfig().get('recordsPerPage') || 20;
                })

        this.wait(promise);
    }

    afterRender() {
        const viewName = this.getMetadata()
                .get(['clientDefs', 'Notification', 'recordViews', 'list']) ||
            'views/notification/record/list';

        const options = {
            selector: '.list-container',
            collection: this.collection,
            showCount: false,
            listLayout: {
                rows: [
                    [
                        {
                            name: 'data',
                            view: 'views/notification/fields/container',
                            options: {
                                containerSelector: this.getSelector(),
                            },
                        },
                    ],
                ],
                right: {
                    name: 'read',
                    view: 'views/notification/fields/read-with-menu',
                    width: '10px',
                },
            },
        };

        this.collection
            .fetch()
            .then(() => this.createView('list', viewName, options))
            .then(view => view.render())
            .then(view => {
                view.$el.find('> .list > .list-group');
            });
    }

    actionMarkAllRead() {
        Espo.Ajax.postRequest('Notification/action/markAllRead')
            .then(() => {
                this.trigger('all-read');

                this.$el.find('.badge-circle-warning').remove();
            });
    }

    
    getRecordView() {
        return this.getView('list');
    }
}

export default NotificationListView;
