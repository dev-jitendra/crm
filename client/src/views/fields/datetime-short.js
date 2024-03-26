



import DatetimeFieldView from 'views/fields/datetime';
import moment from 'moment';

class DatetimeShortFieldView extends DatetimeFieldView {

    listTemplate = 'fields/datetime-short/list'
    detailTemplate = 'fields/datetime-short/detail'

    data() {
        let data = super.data();

        if (this.mode === this.MODE_LIST || this.mode === this.MODE_DETAIL) {
            data.fullDateValue = super.getDateStringValue();
        }

        return data;
    }

    getDateStringValue() {
        if (!(this.mode === this.MODE_LIST || this.mode === this.MODE_DETAIL)) {
            return super.getDateStringValue();
        }

        let value = this.model.get(this.name)

        if (!value) {
            return super.getDateStringValue();
        }

        let timeFormat = this.getDateTime().timeFormat;

        if (this.params.hasSeconds) {
            timeFormat = timeFormat.replace(/:mm/, ':mm:ss');
        }

        let m = this.getDateTime().toMoment(value);
        let now = moment().tz(this.getDateTime().timeZone || 'UTC');

        if (
            m.unix() > now.clone().startOf('day').unix() &&
            m.unix() < now.clone().add(1, 'days').startOf('day').unix()
        ) {
            return m.format(timeFormat);
        }

        let readableFormat = this.getDateTime().getReadableShortDateFormat();

        return m.format('YYYY') === now.format('YYYY') ?
            m.format(readableFormat) :
            m.format(readableFormat + ', YY');
    }
}


export default DatetimeShortFieldView;
