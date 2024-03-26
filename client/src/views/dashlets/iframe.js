

import BaseDashletView from 'views/dashlets/abstract/base';

class IframeDashletView extends BaseDashletView {

    name = 'Iframe'

    templateContent = '<iframe style="margin: 0; border: 0;"></iframe>'

    afterRender() {
        const $iframe = this.$el.find('iframe');

        const url = this.getOption('url');

        if (url) {
            $iframe.attr('src', url);
        }

        this.$el.addClass('no-padding');
        this.$el.css('overflow', 'hidden');

        const height = this.$el.height();

        $iframe.css('height', height);
        $iframe.css('width', '100%');
    }

    afterAdding() {
        this.getContainerView().actionOptions();
    }
}

export default IframeDashletView;
