

define('views/email/record/panels/default-side', ['views/record/panels/default-side'], function (Dep) {

    return Dep.extend({

        setupFields: function () {
            Dep.prototype.setupFields.call(this);

            this.fieldList.push({
                name: 'hasAttachment',
                view: 'views/email/fields/has-attachment',
                noLabel: true,
            });

            this.controlHasAttachmentField();

            this.listenTo(this.model, 'change:hasAttachment', this.controlHasAttachmentField, this);
        },

        controlHasAttachmentField: function () {
            if (this.model.get('hasAttachment')) {
                this.recordViewObject.showField('hasAttachment');

                return;
            }

            this.recordViewObject.hideField('hasAttachment');
        },
    });
});
