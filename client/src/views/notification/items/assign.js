

import BaseNotificationItemView from 'views/notification/items/base';

class AssignNotificationItemView extends BaseNotificationItemView {

    messageName = 'assign'

    template = 'notification/items/assign'

    setup() {
        let data = this.model.get('data') || {};

        this.userId = data.userId;

        this.messageData['entityType'] = this.translateEntityType(data.entityType);

        this.messageData['entity'] =
            $('<a>')
                .attr('href', '#' + data.entityType + '/view/' + data.entityId)
                .attr('data-id', data.entityId)
                .attr('data-scope', data.entityType)
                .text(data.entityName);

        this.messageData['user'] =
            $('<a>')
                .attr('href', '#User/view/' + data.userId)
                .attr('data-id', data.userId)
                .attr('data-scope', 'User')
                .text(data.userName);

        this.createMessage();
    }
}

export default AssignNotificationItemView;
