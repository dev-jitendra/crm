

define('crm:views/notification/items/event-attendee', ['views/notification/items/base'], function (Dep) {

    return Dep.extend({

        messageName: 'eventAttendee',

        templateContent: `
            <div class="stream-head-container">
                <div class="pull-left">{{{avatar}}}</div>
                <div class="stream-head-text-container">
                    <span class="text-muted message">{{{message}}}</span>
                </div>
            </div>
            <div class="stream-date-container">
                <span class="text-muted small">{{{createdAt}}}</span>
            </div>
        `,

        setup: function () {
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
        },
    });
});
