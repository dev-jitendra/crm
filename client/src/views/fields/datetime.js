



import DateFieldView from 'views/fields/date';
import moment from 'moment';


class DatetimeFieldView extends DateFieldView {

    type = 'datetime'

    editTemplate = 'fields/datetime/edit'

    validations = ['required', 'datetime', 'after', 'before']

    searchTypeList = [
        'lastSevenDays',
        'ever',
        'isEmpty',
        'currentMonth',
        'lastMonth',
        'nextMonth',
        'currentQuarter',
        'lastQuarter',
        'currentYear',
        'lastYear',
        'today',
        'past',
        'future',
        'lastXDays',
        'nextXDays',
        'olderThanXDays',
        'afterXDays',
        'on',
        'after',
        'before',
        'between',
    ]

    timeFormatMap = {
        'HH:mm': 'H:i',
        'hh:mm A': 'h:i A',
        'hh:mm a': 'h:i a',
        'hh:mmA': 'h:iA',
        'hh:mma': 'h:ia',
    }

    data() {
        let data = super.data();

        data.date = data.time = '';

        let value = this.getDateTime().toDisplay(this.model.get(this.name));

        if (value) {
            let pair = this.splitDatetime(value);

            data.date = pair[0];
            data.time = pair[1];
        }

        return data;
    }

    getDateStringValue() {
        if (this.mode === this.MODE_DETAIL && !this.model.has(this.name)) {
            return -1;
        }

        let value = this.model.get(this.name);

        if (!value) {
            if (
                this.mode === this.MODE_EDIT |
                this.mode === this.MODE_SEARCH |
                this.mode === this.MODE_LIST ||
                this.mode === this.MODE_LIST_LINK
            ) {
                return '';
            }

            return null;
        }

        if (
            this.mode === this.MODE_LIST ||
            this.mode === this.MODE_DETAIL ||
            this.mode === this.MODE_LIST_LINK
        ) {
            if (this.getConfig().get('readableDateFormatDisabled') || this.params.useNumericFormat) {
                return this.getDateTime().toDisplay(value);
            }

            let timeFormat = this.getDateTime().timeFormat;

            if (this.params.hasSeconds) {
                timeFormat = timeFormat.replace(/:mm/, ':mm:ss');
            }

            let d = this.getDateTime().toMoment(value);
            let now = moment().tz(this.getDateTime().timeZone || 'UTC');
            let dt = now.clone().startOf('day');

            let ranges = {
                'today': [dt.unix(), dt.add(1, 'days').unix()],
                'tomorrow': [dt.unix(), dt.add(1, 'days').unix()],
                'yesterday': [dt.add(-3, 'days').unix(), dt.add(1, 'days').unix()]
            };

            if (d.unix() >= ranges['today'][0] && d.unix() < ranges['today'][1]) {
                return this.translate('Today') + ' ' + d.format(timeFormat);
            }
            else if (d.unix() > ranges['tomorrow'][0] && d.unix() < ranges['tomorrow'][1]) {
                return this.translate('Tomorrow') + ' ' + d.format(timeFormat);
            }
            else if (d.unix() > ranges['yesterday'][0] && d.unix() < ranges['yesterday'][1]) {
                return this.translate('Yesterday') + ' ' + d.format(timeFormat);
            }

            let readableFormat = this.getDateTime().getReadableDateFormat();

            if (d.format('YYYY') === now.format('YYYY')) {
                return d.format(readableFormat) + ' ' + d.format(timeFormat);
            }
            else {
                return d.format(readableFormat + ', YYYY') + ' ' + d.format(timeFormat);
            }
        }

        return this.getDateTime().toDisplay(value);
    }

    initTimepicker() {
        let $time = this.$time;

        $time.timepicker({
            step: this.params.minuteStep || 30,
            scrollDefaultNow: true,
            timeFormat: this.timeFormatMap[this.getDateTime().timeFormat],
        });

        $time
            .parent()
            .find('button.time-picker-btn')
            .on('click', () => {
                $time.timepicker('show');
            });
    }

    setDefaultTime() {
        let dtString = moment('2014-01-01 00:00').format(this.getDateTime().getDateTimeFormat()) || '';

        let pair = this.splitDatetime(dtString);

        if (pair.length === 2) {
            this.$time.val(pair[1]);
        }
    }

    splitDatetime(value) {
        let m = moment(value, this.getDateTime().getDateTimeFormat());

        let dateValue = m.format(this.getDateTime().getDateFormat());
        let timeValue = value.substr(dateValue.length + 1);

        return [dateValue, timeValue];
    }

    setup() {
        super.setup();

        this.on('remove', () => this.destroyTimepicker());
        this.on('mode-changed', () => this.destroyTimepicker());
    }

    destroyTimepicker() {
        if (this.$time && this.$time[0]) {
            this.$time.timepicker('remove');
        }
    }

    afterRender() {
        super.afterRender();

        if (this.mode !== this.MODE_EDIT) {
            return;
        }

        this.$date = this.$element;
        let $time = this.$time = this.$el.find('input.time-part');

        this.initTimepicker();

        this.$element.on('change.datetime', () => {
            if (this.$element.val() && !$time.val()) {
                this.setDefaultTime();
                this.trigger('change');
            }
        });

        let timeout = false;
        let isTimeFormatError = false;
        let previousValue = $time.val();

        $time.on('change', () => {
            if (!timeout) {
                if (isTimeFormatError) {
                    $time.val(previousValue);

                    return;
                }

                if (this.noneOption && $time.val() === '' && this.$date.val() !== '') {
                    $time.val(this.noneOption);

                    return;
                }

                this.trigger('change');

                previousValue = $time.val();
            }

            timeout = true;

            setTimeout(() => timeout = false, 100);
        });

        $time.on('timeFormatError', () => {
            isTimeFormatError = true;

            setTimeout(() => isTimeFormatError = false, 50);
        });
    }

    
    parse(string) {
        if (!string) {
            return null;
        }

        return this.getDateTime().fromDisplay(string);
    }

    fetch() {
        let data = {};

        let date = this.$date.val();
        let time = this.$time.val();

        let value = null;

        if (date !== '' && time !== '') {
            value = this.parse(date + ' ' + time);
        }

        data[this.name] = value;

        return data;
    }

    
    validateDatetime() {
        if (this.model.get(this.name) === -1) {
            let msg = this.translate('fieldShouldBeDatetime', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }
    }

    
    fetchSearch() {
        let data = super.fetchSearch();

        if (data) {
            data.dateTime = true;
        }

        return data;
    }
}

export default DatetimeFieldView;
