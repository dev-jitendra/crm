



import View from 'view';

class HeaderView extends View {

    template = 'header'

    data() {
        const data = {};

        if ('getHeader' in this.getParentMainView()) {
            data.header = this.getParentMainView().getHeader();
        }

        data.scope = this.scope || this.getParentMainView().scope;
        data.items = this.getItems();

        const dropdown = (data.items || {}).dropdown || [];

        data.hasVisibleDropdownItems = false;

        dropdown.forEach(item => {
            if (!item.hidden) {
                data.hasVisibleDropdownItems = true;
            }
        });

        data.noBreakWords = this.options.fontSizeFlexible;

        data.isXsSingleRow = this.options.isXsSingleRow;

        if ((data.items.buttons || []).length < 2) {
            data.isHeaderAdditionalSpace = true;
        }

        return data;
    }

    setup() {
        this.scope = this.options.scope;

        if (this.model) {
            this.listenTo(this.model, 'after:save', () => {
                if (this.isRendered()) {
                    this.reRender();
                }
            });
        }

        this.wasRendered = false;
    }


    afterRender() {
        if (this.options.fontSizeFlexible) {
            this.adjustFontSize();
        }

        if (this.wasRendered) {
            this.getParentMainView().trigger('header-rendered');
        }

        this.wasRendered = true;
    }

    adjustFontSize(step) {
        step = step || 0;

        if (!step) {
            this.fontSizePercentage = 100;
        }

        const $container = this.$el.find('.header-breadcrumbs');
        const containerWidth = $container.width();
        let childrenWidth = 0;

        $container.children().each((i, el) => {
            childrenWidth += $(el).outerWidth(true);
        });

        if (containerWidth < childrenWidth) {
            if (step > 7) {
                $container.addClass('overlapped');

                this.$el.find('.title').each((i, el) => {
                    const $el = $(el);
                    const text = $(el).text();

                    $el.attr('title', text);

                    let isInitialized = false;

                    $el.on('touchstart', () => {
                        if (!isInitialized) {
                            $el.attr('title', '');
                            isInitialized = true;

                            Espo.Ui.popover($el, {
                                content: text,
                                noToggleInit: true,
                            }, this);
                        }

                        $el.popover('toggle');
                    });
                });

                return;
            }

            this.fontSizePercentage -= 4;

            const $flexible = this.$el.find('.font-size-flexible');

            $flexible.css('font-size', this.fontSizePercentage + '%');
            $flexible.css('position', 'relative');

            if (step > 6) {
                $flexible.css('top', '-1px');
            } else if (step > 4) {
                $flexible.css('top', '-1px');
            }

            this.adjustFontSize(step + 1);
        }
    }

    getItems() {
        return this.getParentMainView().getMenu() || {};
    }

    
    getParentMainView() {
        return this.getParentView();
    }
}

export default HeaderView;
