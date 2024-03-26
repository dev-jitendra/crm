

import BaseNotificationItemView from 'views/notification/items/base';
import {marked} from 'marked';
import DOMPurify from 'dompurify';

class MessageNotificationItemView extends BaseNotificationItemView {

    template = 'notification/items/message'

    data() {
        return {
            ...super.data(),
            style: this.style,
        };
    }

    setup() {
        let data = this.model.get('data') || {};

        let messageRaw = this.model.get('message') || data.message || '';
        let message = marked.parse(messageRaw);

        this.messageTemplate = DOMPurify.sanitize(message, {}).toString();

        this.userId = data.userId;
        this.style = data.style || 'text-muted';

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

export default MessageNotificationItemView;
