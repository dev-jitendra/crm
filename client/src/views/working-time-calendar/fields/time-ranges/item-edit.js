

define('views/working-time-calendar/fields/time-ranges/item-edit', ['view', 'lib!moment'], function (Dep, moment) {

    return Dep.extend({

        
        templateContent: `
            <div class="row">
                <div class="start-container col-xs-5">
                    <input
                        class="form-control"
                        type="text"
                        data-name="start"
                        value="{{start}}"
                        autocomplete="espo-start"
                        spellcheck="false"
                    >
                </div>
                <div class="start-container col-xs-1 center-align">
                    <span class="field-row-text-item">&nbsp;–&nbsp;</span>
                </div>
                <div class="end-container col-xs-5">
                    <input
                        class="form-control"
                        type="text"
                        data-name="end"
                        value="{{end}}"
                        autocomplete="espo-end"
                        spellcheck="false"
                    >
                </div>
                <div class="col-xs-1 center-align">
                    <a
                        role="button"
                        tabindex="0"
                        class="remove-item field-row-text-item"
                        data-key="{{key}}"
                        title="{{translate 'Remove'}}"
                    ><span class="fas fa-times"></span></a>
                </div>
            </div>
        `,

        timeFormatMap: {
            'HH:mm': 'H:i',
            'hh:mm A': 'h:i A',
            'hh:mm a': 'h:i a',
            'hh:mmA': 'h:iA',
            'hh:mma': 'h:ia',
        },

        minuteStep: 30,

        data: function () {
            let data = {};

            data.start = this.convertTimeToDisplay(this.value[0]);
            data.end = this.convertTimeToDisplay(this.value[1]);

            data.key = this.key;

            return data;
        },

        setup: function () {
            this.value = this.options.value || [null, null];
            this.key = this.options.key;
        },

        convertTimeToDisplay: function (value) {
            if (!value) {
                return '';
            }

            let m = moment(value, 'HH:mm');

            if (!m.isValid()) {
                return '';
            }

            return m.format(this.getDateTime().timeFormat);
        },

        convertTimeFromDisplay: function (value) {
            if (!value) {
                return null;
            }

            let m = moment(value, this.getDateTime().timeFormat);

            if (!m.isValid()) {
                return null;
            }

            return m.format('HH:mm');
        },

        afterRender: function () {
            this.$start = this.$el.find('[data-name="start"]');
            this.$end = this.$el.find('[data-name="end"]');

            this.initTimepicker(this.$start);
            this.initTimepicker(this.$end);

            this.setMinTime();

            this.$start.on('change', () => this.setMinTime());
        },

        setMinTime: function () {
            let value = this.$start.val();

            this.$end.timepicker('option', 'maxTime', this.convertTimeToDisplay('23:59'));

            if (!value) {
                this.$end.timepicker('option', 'minTime', null);

                return;
            }

            this.$end.timepicker('option', 'minTime', value);
        },

        initTimepicker: function ($el) {
            $el.timepicker({
                step: this.minuteStep,
                timeFormat: this.timeFormatMap[this.getDateTime().timeFormat],
            });

            $el.on('change', () => this.trigger('change'));

            $el.attr('autocomplete', 'espo-time-range-item');
        },

        fetch: function () {
            return [
                this.convertTimeFromDisplay(this.$start.val()),
                this.convertTimeFromDisplay(this.$end.val()),
            ];
        },
    });
});
