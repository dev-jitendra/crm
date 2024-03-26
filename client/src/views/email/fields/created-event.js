

define('views/email/fields/created-event', ['views/fields/link-parent'], function (Dep) {

    return class extends Dep {

        data() {
            let data = super.data();

            let icsEventData = this.model.get('icsEventData') || {};

            if (
                this.isReadMode() &&
                !data.idValue &&
                icsEventData.createdEvent
            ) {
                data.idValue = icsEventData.createdEvent.id;
                data.typeValue = icsEventData.createdEvent.entityType;
                data.nameValue = icsEventData.createdEvent.name;
            }

            return data;
        }

        getAttributeList() {
            let list = super.getAttributeList();

            list.push('icsEventData');

            return list;
        }
    };
});
