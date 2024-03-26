

define('crm:views/call/fields/date-end', ['views/fields/datetime'], function (Dep) {

    return Dep.extend({

        validateAfter: function () {
            var field = this.model.getFieldParam(this.name, 'after');

            if (field) {
                var value = this.model.get(this.name);
                var otherValue = this.model.get(field);

                if (value && otherValue) {
                    if (moment(value).unix() < moment(otherValue).unix()) {
                        var msg = this.translate('fieldShouldAfter', 'messages')
                            .replace('{field}', this.getLabelText())
                            .replace('{otherField}', this.translate(field, 'fields', this.entityType));

                        this.showValidationMessage(msg);

                        return true;
                    }
                }
            }
        },
    });
});
