

import ModalView from 'views/modal';

class LastViewedModalView extends ModalView {

    scope = 'ActionHistoryRecord'
    className = 'dialog dialog-record'
    template = 'modals/last-viewed'
    backdrop = true

    setup() {
        this.events['click .list .cell > a'] = () => {
            this.close();
        };

        this.$header = $('<a>')
            .attr('href', '#LastViewed')
            .attr('data-action', 'listView')
            .addClass('action')
            .text(this.getLanguage().translate('LastViewed', 'scopeNamesPlural'));

        this.waitForView('list');

        this.getCollectionFactory().create(this.scope, collection => {
            collection.maxSize = this.getConfig().get('recordsPerPage');
            collection.url = 'LastViewed';

            this.collection = collection;

            this.loadList();

            collection.fetch();
        });
    }

    
    actionListView() {
        this.getRouter().navigate('#LastViewed', {trigger: true});

        this.close();
    }

    loadList() {
        const viewName = this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.listLastViewed') ||
            'views/record/list';

        this.listenToOnce(this.collection, 'sync', () => {
            this.createView('list', viewName, {
                collection: this.collection,
                fullSelector: this.containerSelector + ' .list-container',
                selectable: false,
                checkboxes: false,
                massActionsDisabled: true,
                rowActionsView: false,
                searchManager: this.searchManager,
                checkAllResultDisabled: true,
                buttonsDisabled: true,
                headerDisabled: true,
                layoutName: 'listForLastViewed',
                layoutAclDisabled: true,
            });
        });
    }
}


export default LastViewedModalView;
