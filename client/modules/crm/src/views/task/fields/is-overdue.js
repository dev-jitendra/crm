

define('crm:views/task/fields/is-overdue', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        readOnly: true,

        templateContent: `
            {{#if isOverdue}}
            <span class="label label-danger">{{translate "overdue" scope="Task"}}</span>
            {{/if}}
        `,

        data: function () {
            var isOverdue = false;

            if (['Completed', 'Canceled'].indexOf(this.model.get('status')) === -1) {
                if (this.model.has('dateEnd')) {
                    if (!this.isDate()) {
                        let value = this.model.get('dateEnd');

                        if (value) {
                            let d = this.getDateTime().toMoment(value);
                            let now = moment().tz(this.getDateTime().timeZone || 'UTC');

                            if (d.unix() < now.unix()) {
                                isOverdue = true;
                            }
                        }
                    } else {
                        let value = this.model.get('dateEndDate');

                        if (value) {
                            let d = moment.utc(value + ' 23:59', this.getDateTime().internalDateTimeFormat);
                            let now = this.getDateTime().getNowMoment();

                            if (d.unix() < now.unix()) {
                                isOverdue = true;
                            }
                        }
                    }

                }
            }

            return {
                isOverdue: isOverdue,
            };
        },

        setup: function () {
            this.mode = 'detail';
        },

        isDate: function () {
            var dateValue = this.model.get('dateEnd');

            if (dateValue) {
                return true;
            }

            return false;
        },
    });
});
