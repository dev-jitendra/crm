

import View from 'view';

class NotificationPanelView extends View {

    template = 'notification/panel'

    setup() {
        this.addActionHandler('markAllNotificationsRead', () => this.actionMarkAllRead());
        this.addActionHandler('openNotifications', () => this.actionOpenNotifications());
        this.addActionHandler('closePanel', () => this.close());

        this.addHandler('keydown', '', event => {
            if (event.code === 'Escape') {
                this.close();
            }
        })

        const promise =
            this.getCollectionFactory().create('Notification', collection => {
                this.collection = collection;
                this.collection.maxSize = this.getConfig().get('notificationsMaxSize') || 5;

                this.listenTo(this.collection, 'sync', () => {
                    this.trigger('collection-fetched');
                });
            });

        this.wait(promise);

        this.navbarPanelHeightSpace = this.getThemeManager().getParam('navbarPanelHeightSpace') || 100;
        this.navbarPanelBodyMaxHeight = this.getThemeManager().getParam('navbarPanelBodyMaxHeight') || 600;

        this.once('remove', () => {
            $(window).off('resize.notifications-height');

            if (this.overflowWasHidden) {
                $('body').css('overflow', 'unset');

                this.overflowWasHidden = false;
            }

            if (this.collection) {
                this.collection.abortLastFetch();
            }
        });
    }

    afterRender() {
        this.collection.fetch()
            .then(() => this.createRecordView())
            .then(view => view.render());

        const $window = $(window);

        $window.off('resize.notifications-height');
        $window.on('resize.notifications-height', this.processSizing.bind(this));

        this.processSizing();

        $('#navbar li.notifications-badge-container').addClass('open');

        this.$el.find('> .panel').focus();
    }

    onRemove() {
        $('#navbar li.notifications-badge-container').removeClass('open');
    }

    
    createRecordView() {
        const viewName = this.getMetadata().get(['clientDefs', 'Notification', 'recordViews', 'list']) ||
            'views/notification/record/list';

        return this.createView('list', viewName, {
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
                        }
                    ]
                ],
                right: {
                    name: 'read',
                    view: 'views/notification/fields/read',
                    width: '10px',
                },
            }
        });
    }

    actionMarkAllRead() {
        Espo.Ajax.postRequest('Notification/action/markAllRead')
            .then(() => this.trigger('all-read'));
    }

    processSizing() {
        const $window = $(window);
        const windowHeight = $window.height();
        const windowWidth = $window.width();

        const diffHeight = this.$el.find('.panel-heading').outerHeight();

        const cssParams = {};

        if (windowWidth <= this.getThemeManager().getParam('screenWidthXs')) {
            cssParams.height = (windowHeight - diffHeight) + 'px';
            cssParams.overflow = 'auto';

            $('body').css('overflow', 'hidden');
            this.overflowWasHidden = true;

            this.$el.find('.panel-body').css(cssParams);

            return;
        }

        cssParams.height = 'unset';
        cssParams.overflow = 'none';

        if (this.overflowWasHidden) {
            $('body').css('overflow', 'unset');

            this.overflowWasHidden = false;
        }

        if (windowHeight - this.navbarPanelBodyMaxHeight < this.navbarPanelHeightSpace) {
            const maxHeight = windowHeight - this.navbarPanelHeightSpace;

            cssParams.maxHeight = maxHeight + 'px';
        }

        this.$el.find('.panel-body').css(cssParams);
    }

    close() {
        this.trigger('close');
    }

    actionOpenNotifications() {
        this.getRouter().navigate('#Notification', {trigger: true});

        this.close();
    }
}

export default NotificationPanelView;
