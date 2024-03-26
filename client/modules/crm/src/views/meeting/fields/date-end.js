

define('crm:views/meeting/fields/date-end', ['views/fields/datetime-optional'], function (Dep) {

    return Dep.extend({

        validateAfterAllowSameDay: true,
        emptyTimeInInlineEditDisabled: true,
        noneOptionIsHidden: true,
        isEnd: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.isAllDayValue = this.model.get('isAllDay');

            this.listenTo(this.model, 'change:isAllDay', (model, value, o) => {
                if (!o.ui) {
                    return;
                }

                if (!this.isEditMode()) {
                    return;
                }

                if (this.isAllDayValue === undefined && !value) {
                    this.isAllDayValue = value;

                    return;
                }

                this.isAllDayValue = value;

                if (value) {
                    this.$time.val(this.noneOption);
                } else {
                    let dateTime = this.model.get('dateStart');

                    if (!dateTime) {
                        dateTime = this.getDateTime().getNow(5);
                    }

                    let m = this.getDateTime().toMoment(dateTime);
                    dateTime = m.format(this.getDateTime().getDateTimeFormat());

                    let index = dateTime.indexOf(' ');
                    let time = dateTime.substring(index + 1);

                    if (this.model.get('dateEnd')) {
                        this.$time.val(time);
                    }
                }

                this.trigger('change');
                this.controlTimePartVisibility();
            });
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.isEditMode()) {
                this.controlTimePartVisibility();
            }
        },

        controlTimePartVisibility: function () {
            if (!this.isEditMode()) {
                return;
            }

            if (this.model.get('isAllDay')) {
                this.$time.addClass('hidden');
                this.$el.find('.time-picker-btn').addClass('hidden');

                return;
            }

            this.$time.removeClass('hidden');
            this.$el.find('.time-picker-btn').removeClass('hidden');
        },
    });
});
