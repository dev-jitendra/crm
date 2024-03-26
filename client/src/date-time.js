



import moment from 'moment';


class DateTime {

    constructor() {}

    
    internalDateFormat = 'YYYY-MM-DD'

    
    internalDateTimeFormat = 'YYYY-MM-DD HH:mm'

    
    internalDateTimeFullFormat = 'YYYY-MM-DD HH:mm:ss'

    
    dateFormat = 'MM/DD/YYYY'

    
    timeFormat = 'HH:mm'

    
    timeZone = null

    
    weekStart = 1

    
    readableDateFormatMap = {
        'DD.MM.YYYY': 'DD MMM',
        'DD/MM/YYYY': 'DD MMM',
    }

    
    readableShortDateFormatMap = {
        'DD.MM.YYYY': 'D MMM',
        'DD/MM/YYYY': 'D MMM',
    }

    
    hasMeridian() {
        return (new RegExp('A', 'i')).test(this.timeFormat);
    }

    
    getDateFormat() {
        return this.dateFormat;
    }

    
    getTimeFormat() {
        return this.timeFormat;
    }

    
    getDateTimeFormat() {
        return this.dateFormat + ' ' + this.timeFormat;
    }

    
    getReadableDateFormat() {
        return this.readableDateFormatMap[this.getDateFormat()] || 'MMM DD';
    }

    
    getReadableShortDateFormat() {
        return this.readableShortDateFormatMap[this.getDateFormat()] || 'MMM D';
    }

    
    
    getReadableDateTimeFormat() {
        return this.getReadableDateFormat() + ' ' + this.timeFormat;
    }

    
    getReadableShortDateTimeFormat() {
        return this.getReadableShortDateFormat() + ' ' + this.timeFormat;
    }

    
    fromDisplayDate(string) {
        const m = moment(string, this.dateFormat);

        if (!m.isValid()) {
            return -1;
        }

        return m.format(this.internalDateFormat);
    }

    
    getTimeZone() {
        return this.timeZone ? this.timeZone : 'UTC';
    }

    
    toDisplayDate(string) {
        if (!string || (typeof string !== 'string')) {
            return '';
        }

        const m = moment(string, this.internalDateFormat);

        if (!m.isValid()) {
            return '';
        }

        return m.format(this.dateFormat);
    }

    
    fromDisplay(string) {
        let m;

        if (this.timeZone) {
            m = moment.tz(string, this.getDateTimeFormat(), this.timeZone).utc();
        }
        else {
            m = moment.utc(string, this.getDateTimeFormat());
        }

        if (!m.isValid()) {
            return -1;
        }

        return m.format(this.internalDateTimeFormat) + ':00';
    }

    
    toDisplay(string) {
        if (!string) {
            return '';
        }

        return this.toMoment(string).format(this.getDateTimeFormat());
    }

    
    getNowMoment() {
        return moment().tz(this.getTimeZone())
    }

    
    toMomentDate(string) {
        return moment.utc(string, this.internalDateFormat);
    }

    
    toMoment(string) {
        let m = moment.utc(string, this.internalDateTimeFullFormat);

        if (this.timeZone) {
            
            m = m.tz(this.timeZone);
        }

        return m;
    }

    
    fromIso(string) {
        if (!string) {
            return '';
        }

        const m = moment(string).utc();

        return m.format(this.internalDateTimeFormat);
    }

    
    
    toIso(string) {
        return this.toMoment(string).format();
    }

    
    getToday() {
        return moment().tz(this.getTimeZone()).format(this.internalDateFormat);
    }

    
    getDateTimeShiftedFromNow(shift, type, multiplicity) {
        if (!multiplicity) {
            return moment.utc().add(shift, type).format(this.internalDateTimeFormat);
        }

        let unix = moment().unix();

        unix = unix - (unix % (multiplicity * 60));

        return moment.unix(unix).utc().add(shift, type).format(this.internalDateTimeFormat);
    }

    
    getDateShiftedFromToday(shift, type) {
        return moment.tz(this.getTimeZone()).add(shift, type).format(this.internalDateFormat);
    }

    
    getNow(multiplicity) {
        if (!multiplicity) {
            return moment.utc().format(this.internalDateTimeFormat);
        }

        let unix = moment().unix();

        unix = unix - (unix % (multiplicity * 60));

        return moment.unix(unix).utc().format(this.internalDateTimeFormat);
    }

    
    setSettingsAndPreferences(settings, preferences) {
        if (settings.has('dateFormat')) {
            this.dateFormat = settings.get('dateFormat');
        }

        if (settings.has('timeFormat')) {
            this.timeFormat = settings.get('timeFormat');
        }

        if (settings.has('timeZone')) {
            this.timeZone = settings.get('timeZone') || null;

            if (this.timeZone === 'UTC') {
                this.timeZone = null;
            }
        }

        if (settings.has('weekStart')) {
            this.weekStart = settings.get('weekStart');
        }

        preferences.on('change', model => {
            if (model.has('dateFormat') && model.get('dateFormat')) {
                this.dateFormat = model.get('dateFormat');
            }

            if (model.has('timeFormat') && model.get('timeFormat')) {
                this.timeFormat = model.get('timeFormat');
            }

            if (model.has('timeZone') && model.get('timeZone')) {

                this.timeZone = model.get('timeZone');
            }

            if (model.has('weekStart') && model.get('weekStart') !== -1) {
                this.weekStart = model.get('weekStart');
            }

            if (this.timeZone === 'UTC') {
                this.timeZone = null;
            }
        });
    }

    
    setLanguage(language) {
        moment.updateLocale('en', {
            months: language.translatePath(['Global', 'lists', 'monthNames']),
            monthsShort: language.translatePath(['Global', 'lists', 'monthNamesShort']),
            weekdays: language.translatePath(['Global', 'lists', 'dayNames']),
            weekdaysShort: language.translatePath(['Global', 'lists', 'dayNamesShort']),
            weekdaysMin: language.translatePath(['Global', 'lists', 'dayNamesMin']),
        });

        moment.locale('en');
    }
}

export default DateTime;
