

import BaseFieldView from 'views/fields/base';

class NotificationReadFieldView extends BaseFieldView {

    type = 'read'
    listTemplate = 'notification/fields/read'
    detailTemplate = 'notification/fields/read'

    data() {
        return {
            isRead: this.model.get('read'),
        };
    }
}

export default NotificationReadFieldView;
