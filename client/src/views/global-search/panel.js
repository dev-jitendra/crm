

import View from 'view';

class GlobalSearchPanel extends View {

    template = 'global-search/panel'

    setup() {
        this.addHandler('click', '[data-action="closePanel"]', () => this.close());

        this.maxSize = this.getConfig().get('globalSearchMaxSize') || 10;

        this.navbarPanelHeightSpace = this.getThemeManager().getParam('navbarPanelHeightSpace') || 100;
        this.navbarPanelBodyMaxHeight = this.getThemeManager().getParam('navbarPanelBodyMaxHeight') || 600;
    }

    onRemove() {
        $(window).off('resize.global-search-height');

        if (this.overflowWasHidden) {
            $('body').css('overflow', 'unset');

            this.overflowWasHidden = false;
        }
    }

    afterRender() {
        this.collection.reset();
        this.collection.maxSize = this.maxSize;

        this.collection.fetch()
            .then(() => this.createRecordView())
            .then(view => view.render());

        const $window = $(window);

        $window.off('resize.global-search-height');
        $window.on('resize.global-search-height', this.processSizing.bind(this));

        this.processSizing();
    }

    
    createRecordView() {
        return this.createView('list', 'views/record/list-expanded', {
            selector: '.list-container',
            collection: this.collection,
            listLayout: {
                rows: [
                    [
                        {
                            name: 'name',
                            view: 'views/global-search/name-field',
                        }
                    ]
                ],
                right: {
                    name: 'read',
                    view: 'views/global-search/scope-badge',
                    width: '80px',
                },
            }
        });
    }

    processSizing() {
        const $window = $(window);

        let windowHeight = $window.height();
        let windowWidth = $window.width();

        let diffHeight = this.$el.find('.panel-heading').outerHeight();

        let cssParams = {};

        if (windowWidth <= this.getThemeManager().getParam('screenWidthXs')) {
            cssParams.height = (windowHeight - diffHeight) + 'px';
            cssParams.overflow = 'auto';

            $('body').css('overflow', 'hidden');

            this.overflowWasHidden = true;
        }
        else {
            cssParams.height = 'unset';
            cssParams.overflow = 'none';

            if (this.overflowWasHidden) {
                $('body').css('overflow', 'unset');

                this.overflowWasHidden = false;
            }

            if (windowHeight - this.navbarPanelBodyMaxHeight < this.navbarPanelHeightSpace) {
                let maxHeight = windowHeight - this.navbarPanelHeightSpace;

                cssParams.maxHeight = maxHeight + 'px';
            }
        }

        this.$el.find('.panel-body').css(cssParams);
    }

    close() {
        this.trigger('close');
    }
}

export default GlobalSearchPanel;
