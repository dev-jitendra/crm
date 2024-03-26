

import BaseNotificationItemView from 'views/notification/items/base';

class SystemNotificationItemView extends BaseNotificationItemView {

    template = 'notification/items/system'

    data() {
        return {
            ...super.data(),
            message: this.model.get('message'),
        };
    }

    setup() {
        let data = this.model.get('data') || {};

        this.userId = data.userId;
    }
}

export default SystemNotificationItemView;
