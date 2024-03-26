

import BaseNotificationItemView from 'views/notification/items/base';

class EmailReceivedNotificationItemView extends BaseNotificationItemView {

    messageName = 'emailReceived'

    template = 'notification/items/email-received'

    data() {
        return {
            ...super.data(),
            emailId: this.emailId,
            emailName: this.emailName,
        };
    }

    setup() {
        let data = this.model.get('data') || {};

        this.userId = data.userId;

        this.messageData['entityType'] = this.translateEntityType(data.entityType);

        if (data.personEntityId) {
            this.messageData['from'] =
                $('<a>')
                    .attr('href', '#' + data.personEntityType + '/view/' + data.personEntityId)
                    .attr('data-id', data.personEntityId)
                    .attr('data-scope', data.personEntityType)
                    .text(data.personEntityName);
        }
        else {
            let text = data.fromString || this.translate('empty address');

            this.messageData['from'] = $('<span>').text(text);
        }

        this.emailId = data.emailId;
        this.emailName = data.emailName;

        this.createMessage();
    }
}

export default EmailReceivedNotificationItemView;
