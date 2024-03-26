

import BaseFieldView from 'views/fields/base';

class NotificationReadWithMenuFieldView extends BaseFieldView {

    type = 'read'
    listTemplate = 'notification/fields/read-with-menu'
    detailTemplate = 'notification/fields/read-with-menu'

    data() {
        return {
            isRead: this.model.get('read'),
        };
    }
}

export default NotificationReadWithMenuFieldView;
