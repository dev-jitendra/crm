

define('views/working-time-range/fields/date-end', ['views/fields/date'], function (Dep) {


    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.validations.push('afterOrSame');
        },

        validateAfterOrSame: function () {
            let field = 'dateStart';

            let value = this.model.get(this.name);
            let otherValue = this.model.get(field);

            if (value && otherValue) {
                if (moment(value).unix() < moment(otherValue).unix()) {
                    let msg = this.translate('fieldShouldAfter', 'messages')
                        .replace('{field}', this.getLabelText())
                        .replace('{otherField}', this.translate(field, 'fields', this.model.entityType));

                    this.showValidationMessage(msg);

                    return true;
                }
            }

            return false;
        },
    });
});
