

import View from 'view';

class ClearCacheView extends View {

    template = 'clear-cache'

    el = '> body'

    events = {
        
        'click .action[data-action="clearLocalCache"]': function () {
            this.clearLocalCache();
        },
        
        'click .action[data-action="returnToApplication"]': function () {
            this.returnToApplication();
        }
    }

    data() {
        return {
            cacheIsEnabled: !!this.options.cache
        };
    }

    clearLocalCache() {
        this.options.cache.clear();

        this.$el.find('.action[data-action="clearLocalCache"]').remove();
        this.$el.find('.message-container').removeClass('hidden');
        this.$el.find('.message-container span').html(this.translate('Cache has been cleared'));
        this.$el.find('.action[data-action="returnToApplication"]').removeClass('hidden');
    }

    returnToApplication() {
        this.getRouter().navigate('', {trigger: true});
    }
}

export default ClearCacheView;
