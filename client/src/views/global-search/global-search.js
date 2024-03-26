

import View from 'view';

class GlobalSearchView extends View {

    template = 'global-search/global-search'

    setup() {
        this.addHandler('keydown', 'input.global-search-input', 'onKeydown');
        this.addHandler('focus', 'input.global-search-input', 'onFocus');
        this.addHandler('click', '[data-action="search"]', () => this.runSearch());

        let promise = this.getCollectionFactory().create('GlobalSearch', collection => {
            this.collection = collection;
            this.collection.url = 'GlobalSearch';
        });

        this.wait(promise);

        this.closeNavbarOnShow = /iPad|iPhone|iPod/.test(navigator.userAgent);
    }

    
    onFocus(e) {
        let inputElement = e.target;

        inputElement.select();
    }

    
    onKeydown(e) {
        let key = Espo.Utils.getKeyFromKeyEvent(e);

        if (e.code === 'Enter' || key === 'Enter' || key === 'Control+Enter') {
            this.runSearch();

            return;
        }

        if (key === 'Escape') {
            this.closePanel();
        }
    }

    afterRender() {
        this.$input = this.$el.find('input.global-search-input');
    }

    runSearch() {
        let text = this.$input.val().trim();

        if (text !== '' && text.length >= 2) {
            this.search(text);
        }
    }

    search(text) {
        this.collection.url = this.collection.urlRoot = 'GlobalSearch?q=' + encodeURIComponent(text);

        this.showPanel();
    }

    showPanel() {
        this.closePanel();

        if (this.closeNavbarOnShow) {
            this.$el.closest('.navbar-body').removeClass('in');
        }

        let $container = $('<div>').attr('id', 'global-search-panel');

        $container.appendTo(this.$el.find('.global-search-panel-container'));

        this.createView('panel', 'views/global-search/panel', {
            fullSelector: '#global-search-panel',
            collection: this.collection,
        }, view => {
            view.render();

            this.listenToOnce(view, 'close', this.closePanel);
        });

        let $document = $(document);

        $document.on('mouseup.global-search', (e) => {
            if (e.which !== 1) {
                return;
            }

            if (!$container.is(e.target) && $container.has(e.target).length === 0) {
                this.closePanel();
            }
        });

        $document.on('click.global-search', (e) => {
            if (
                e.target.tagName === 'A' &&
                $(e.target).data('action') !== 'showMore' &&
                !$(e.target).hasClass('global-search-button')
            ) {
                setTimeout(() => this.closePanel(), 100);
            }
        });
    }

    closePanel() {
        let $container = $('#global-search-panel');

        $container.remove();

        let $document = $(document);

        if (this.hasView('panel')) {
            this.getView('panel').remove();
        }

        $document.off('mouseup.global-search');
        $document.off('click.global-search');
    }
}

export default GlobalSearchView;
