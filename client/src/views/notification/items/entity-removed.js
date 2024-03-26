

import BaseNotificationItemView from 'views/notification/items/base';

class EmailRemovedNotificationItemView extends BaseNotificationItemView {

    messageName = 'entityRemoved'

    template = 'notification/items/entity-removed'

    setup() {
        let data = this.model.get('data') || {};

        this.userId = data.userId;

        this.messageData['entityType'] = this.translateEntityType(data.entityType);

        this.messageData['user'] =
            $('<a>')
                .attr('href', '#User/view/' + data.userId)
                .attr('data-id', data.userId)
                .attr('data-scope', 'User')
                .text(data.userName);

        this.messageData['entity'] =
            $('<a>')
                .attr('href', '#' + data.entityType + '/view/' + data.entityId)
                .attr('data-id', data.entityId)
                .attr('data-scope', data.entityType)
                .text(data.entityName);

        this.createMessage();
    }
}

export default EmailRemovedNotificationItemView;
