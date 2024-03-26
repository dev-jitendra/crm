

define('crm:views/task/fields/date-end', ['views/fields/datetime-optional'], function (Dep) {

    return Dep.extend({

        detailTemplate: 'crm:task/fields/date-end/detail',
        listTemplate: 'crm:task/fields/date-end/detail',

        isEnd: true,

        data: function () {
            var data = Dep.prototype.data.call(this);

            if (this.model.get('status') && !~['Completed', 'Canceled'].indexOf(this.model.get('status'))) {
                if (this.mode === 'list' || this.mode === 'detail') {
                    if (!this.isDate()) {
                        let value = this.model.get(this.name);

                        if (value) {
                            let d = this.getDateTime().toMoment(value);
                            let now = moment().tz(this.getDateTime().timeZone || 'UTC');

                            if (d.unix() < now.unix()) {
                                data.isOverdue = true;
                            }
                        }
                    } else {
                        let value = this.model.get(this.nameDate);

                        if (value) {
                            let d = moment.utc(value + ' 23:59', this.getDateTime().internalDateTimeFormat);
                            let now = this.getDateTime().getNowMoment();

                            if (d.unix() < now.unix()) {
                                data.isOverdue = true;
                            }
                        }
                    }
                }
            }

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this, 'change', () => {
                if (!this.model.get('dateEnd')) {
                    if (this.model.get('reminders')) {
                        this.model.set('reminders', []);
                    }
                }
            });
        },
    });
});
