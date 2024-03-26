

import View from 'view';

class GlobalSearchScopeBadgeView extends View {

    template = 'global-search/scope-badge'

    data() {
        return {
            label: this.translate(this.model.get('_scope'), 'scopeNames'),
        };
    }
}

export default GlobalSearchScopeBadgeView;
