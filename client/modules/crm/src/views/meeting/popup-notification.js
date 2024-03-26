

import PopupNotificationView from 'views/popup-notification';

class MeetingPopupNotificationView extends PopupNotificationView {

    template = 'crm:meeting/popup-notification'

    type = 'event'
    style = 'primary'
    closeButton = true

    setup() {
        if (!this.notificationData.entityType) {
            return;
        }

        let promise = this.getModelFactory().create(this.notificationData.entityType, model => {
            let dateAttribute = 'dateStart';

            if (this.notificationData.entityType === 'Task') {
                dateAttribute = 'dateEnd';
            }

            this.dateAttribute = dateAttribute;

            model.set(dateAttribute, this.notificationData[dateAttribute]);

            this.createView('dateField', 'views/fields/datetime', {
                model: model,
                mode: 'detail',
                selector: '.field[data-name="' + dateAttribute + '"]',
                defs: {
                    name: dateAttribute,
                },
                readOnly: true,
            });
        });

        this.wait(promise);
    }

    data() {
        return {
            header: this.translate(this.notificationData.entityType, 'scopeNames'),
            dateAttribute: this.dateAttribute,
            ...super.data(),
        };
    }

    onCancel() {
        Espo.Ajax.postRequest('Activities/action/removePopupNotification', {id: this.notificationId});
    }
}

export default MeetingPopupNotificationView;
