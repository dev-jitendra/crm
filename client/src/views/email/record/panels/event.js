

define('views/email/record/panels/event', ['views/record/panels/side'], function (Dep) {

    return class extends Dep {

        setupFields() {
            super.setupFields();

            this.fieldList.push({
                name: 'icsEventDateStart',
                readOnly: true,
                labelText: this.translate('dateStart', 'fields', 'Meeting'),
            });

            this.fieldList.push({
                name: 'createdEvent',
                readOnly: true,
            });

            this.fieldList.push({
                name: 'createEvent',
                readOnly: true,
                noLabel: true,
            });

            this.controlEventField();

            this.listenTo(this.model, 'change:icsEventData', this.controlEventField, this);
            this.listenTo(this.model, 'change:createdEventId', this.controlEventField, this);
        }

        controlEventField() {
            if (!this.model.get('icsEventData')) {
                this.recordViewObject.hideField('createEvent');
                this.recordViewObject.showField('createdEvent');

                return;
            }

            const eventData = this.model.get('icsEventData');

            if (eventData.createdEvent) {
                this.recordViewObject.hideField('createEvent');
                this.recordViewObject.showField('createdEvent');

                return;
            }

            if (!this.model.get('createdEventId')) {
                this.recordViewObject.hideField('createdEvent');
                this.recordViewObject.showField('createEvent');

                return;
            }

            this.recordViewObject.hideField('createEvent');
            this.recordViewObject.showField('createdEvent');
        }
    };
});
