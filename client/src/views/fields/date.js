



import BaseFieldView from 'views/fields/base';
import moment from 'moment';


class DateFieldView extends BaseFieldView {

    type = 'date'

    listTemplate = 'fields/date/list'
    listLinkTemplate = 'fields/date/list-link'
    detailTemplate = 'fields/date/detail'
    editTemplate = 'fields/date/edit'
    searchTemplate = 'fields/date/search'

    validations = ['required', 'date', 'after', 'before']

    searchTypeList = [
        'lastSevenDays', 'ever', 'isEmpty', 'currentMonth', 'lastMonth', 'nextMonth', 'currentQuarter',
        'lastQuarter', 'currentYear', 'lastYear', 'today', 'past', 'future', 'lastXDays', 'nextXDays',
        'olderThanXDays', 'afterXDays', 'on', 'after', 'before', 'between',
    ]

    initialSearchIsNotIdle = true

    setup() {
       super.setup();

        if (this.getConfig().get('fiscalYearShift')) {
            this.searchTypeList = Espo.Utils.clone(this.searchTypeList);

            if (this.getConfig().get('fiscalYearShift') % 3 !== 0) {
                this.searchTypeList.push('currentFiscalQuarter');
                this.searchTypeList.push('lastFiscalQuarter');
            }

            this.searchTypeList.push('currentFiscalYear');
            this.searchTypeList.push('lastFiscalYear');
        }
    }

    data() {
        let data = super.data();

        data.dateValue = this.getDateStringValue();

        data.isNone = data.dateValue === null;

        if (data.dateValue === -1) {
            data.dateValue = null;
            data.isLoading = true;
        }

        if (this.isSearchMode()) {
            let value = this.getSearchParamsData().value || this.searchParams.dateValue;
            let valueTo = this.getSearchParamsData().valueTo || this.searchParams.dateValueTo;

            data.dateValue = this.getDateTime().toDisplayDate(value);
            data.dateValueTo = this.getDateTime().toDisplayDate(valueTo);

            if (~['lastXDays', 'nextXDays', 'olderThanXDays', 'afterXDays']
                    .indexOf(this.getSearchType())
            ) {
                data.number = this.searchParams.value;
            }
        }

        return data;
    }

    setupSearch() {
        this.events = _.extend({
            'change select.search-type': (e) => {
                let type = $(e.currentTarget).val();

                this.handleSearchType(type);
            },
        }, this.events || {});
    }

    stringifyDateValue(value) {
        if (!value) {
            if (
                this.mode === this.MODE_EDIT ||
                this.mode === this.MODE_SEARCH ||
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
            return this.convertDateValueForDetail(value);
        }

        return this.getDateTime().toDisplayDate(value);
    }

    convertDateValueForDetail(value) {
        if (this.getConfig().get('readableDateFormatDisabled') || this.params.useNumericFormat) {
            return this.getDateTime().toDisplayDate(value);
        }

        let timezone = this.getDateTime().getTimeZone();
        let internalDateTimeFormat = this.getDateTime().internalDateTimeFormat;
        let readableFormat = this.getDateTime().getReadableDateFormat();
        let valueWithTime = value + ' 00:00:00';

        let today = moment().tz(timezone).startOf('day');
        let dateTime = moment.tz(valueWithTime, internalDateTimeFormat, timezone);

        var temp = today.clone();

        var ranges = {
            'today': [temp.unix(), temp.add(1, 'days').unix()],
            'tomorrow': [temp.unix(), temp.add(1, 'days').unix()],
            'yesterday': [temp.add(-3, 'days').unix(), temp.add(1, 'days').unix()],
        };

        if (dateTime.unix() >= ranges['today'][0] && dateTime.unix() < ranges['today'][1]) {
            return this.translate('Today');
        }

        if (dateTime.unix() >= ranges['tomorrow'][0] && dateTime.unix() < ranges['tomorrow'][1]) {
            return this.translate('Tomorrow');
        }

        if (dateTime.unix() >= ranges['yesterday'][0] && dateTime.unix() < ranges['yesterday'][1]) {
            return this.translate('Yesterday');
        }

        
        dateTime = moment.utc(valueWithTime, internalDateTimeFormat);

        if (dateTime.format('YYYY') === today.format('YYYY')) {
            return dateTime.format(readableFormat);
        }

        return dateTime.format(readableFormat + ', YYYY');
    }

    getDateStringValue() {
        if (this.mode === this.MODE_DETAIL && !this.model.has(this.name)) {
            return -1;
        }

        var value = this.model.get(this.name);

        return this.stringifyDateValue(value);
    }

    afterRender() {
        if (this.mode === this.MODE_EDIT || this.mode === this.MODE_SEARCH) {
            this.$element = this.$el.find('[data-name="' + this.name + '"]');

            let wait = false;

            

            this.$element.on('change', (e) => {
                if (!wait) {
                    this.trigger('change');
                    wait = true;
                    setTimeout(() => wait = false, 100);
                }

                if (e.isTrigger) {
                    if (document.activeElement !== this.$element.get(0)) {
                        this.$element.focus();
                    }
                }
            });

            this.$element.on('click', () => {
                this.$element.datepicker('show');
            });

            let options = {
                format: this.getDateTime().dateFormat.toLowerCase(),
                weekStart: this.getDateTime().weekStart,
                autoclose: true,
                todayHighlight: true,
                keyboardNavigation: true,
                todayBtn: this.getConfig().get('datepickerTodayButton') || false,
                orientation: 'bottom auto',
                templates: {
                    leftArrow: '<span class="fas fa-chevron-left fa-sm"></span>',
                    rightArrow: '<span class="fas fa-chevron-right fa-sm"></span>',
                },
                container: this.$el.closest('.modal-body').length ?
                    this.$el.closest('.modal-body') :
                    'body',
            };

            let language = this.getConfig().get('language');

            if (!(language in $.fn.datepicker.dates)) {
                $.fn.datepicker.dates[language] = {
                    days: this.translate('dayNames', 'lists'),
                    daysShort: this.translate('dayNamesShort', 'lists'),
                    daysMin: this.translate('dayNamesMin', 'lists'),
                    months: this.translate('monthNames', 'lists'),
                    monthsShort: this.translate('monthNamesShort', 'lists'),
                    today: this.translate('Today'),
                    clear: this.translate('Clear'),
                };
            }

            options.language = language;

            this.$element.datepicker(options);

            if (this.mode === this.MODE_SEARCH) {
                let $elAdd = this.$el.find('input.additional');

                $elAdd.datepicker(options);

                $elAdd.parent().find('button.date-picker-btn').on('click', () => {
                    $elAdd.datepicker('show');
                });

                this.$el.find('select.search-type').on('change', () => {
                    this.trigger('change');
                });

                this.$el.find('input.number').on('change', () => {
                    this.trigger('change');
                });

                $elAdd.on('change', e => {
                    this.trigger('change');

                    if (e.isTrigger) {
                        if (document.activeElement !== $elAdd.get(0)) {
                            $elAdd.focus();
                        }
                    }
                });

                $elAdd.on('click', () => {
                    $elAdd.datepicker('show');
                });
            }

            this.$element.parent().find('button.date-picker-btn').on('click', () => {
                this.$element.datepicker('show');
            });

            if (this.mode === this.MODE_SEARCH) {
                let $searchType = this.$el.find('select.search-type');

                this.handleSearchType($searchType.val());
            }
        }
    }

    handleSearchType(type) {
        this.$el.find('div.primary').addClass('hidden');
        this.$el.find('div.additional').addClass('hidden');
        this.$el.find('div.additional-number').addClass('hidden');

        if (~['on', 'notOn', 'after', 'before'].indexOf(type)) {
            this.$el.find('div.primary').removeClass('hidden');
        }
        else if (~['lastXDays', 'nextXDays', 'olderThanXDays', 'afterXDays'].indexOf(type)) {
            this.$el.find('div.additional-number').removeClass('hidden');
        }
        else if (type === 'between') {
            this.$el.find('div.primary').removeClass('hidden');
            this.$el.find('div.additional').removeClass('hidden');
        }
    }

    parseDate(string) {
        return this.getDateTime().fromDisplayDate(string);
    }

    
    parse(string) {
        if (!string) {
            return null;
        }

        return this.parseDate(string);
    }

    
    fetch() {
        let data = {};

        data[this.name] = this.parse(this.$element.val());

        return data;
    }

    
    fetchSearch() {
        let value = this.parseDate(this.$element.val());

        let type = this.fetchSearchType();
        let data;

        if (type === 'between') {
            if (!value) {
                return null;
            }

            let valueTo = this.parseDate(this.$el.find('input.additional').val());

            if (!valueTo) {
                return null;
            }

            data = {
                type: type,
                value: [value, valueTo],
                data: {
                    value: value,
                    valueTo: valueTo
                },
            };
        } else if (~['lastXDays', 'nextXDays', 'olderThanXDays', 'afterXDays'].indexOf(type)) {
            let number = this.$el.find('input.number').val();

            data = {
                type: type,
                value: number,
            };
        }
        else if (~['on', 'notOn', 'after', 'before'].indexOf(type)) {
            if (!value) {
                return null;
            }

            data = {
                type: type,
                value: value,
                data: {
                    value: value,
                },
            };
        }
        else if (type === 'isEmpty') {
            data = {
                type: 'isNull',
                data: {
                    type: type,
                },
            };
        }
        else {
            data = {
                type: type,
            };
        }

        return data;
    }

    getSearchType() {
        return this.getSearchParamsData().type || this.searchParams.typeFront || this.searchParams.type;
    }

    validateRequired() {
        if (!this.isRequired()) {
            return;
        }

        if (this.model.get(this.name) === null) {
            let msg = this.translate('fieldIsRequired', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }
    }

    
    validateDate() {
        if (this.model.get(this.name) === -1) {
            let msg = this.translate('fieldShouldBeDate', 'messages')
                .replace('{field}', this.getLabelText());

            this.showValidationMessage(msg);

            return true;
        }
    }

    
    validateAfter() {
        let field = this.model.getFieldParam(this.name, 'after');

        if (!field) {
            return false;
        }

        let value = this.model.get(this.name);
        let otherValue = this.model.get(field);

        if (!(value && otherValue)) {
            return;
        }

        if (moment(value).unix() <= moment(otherValue).unix()) {
            let msg = this.translate('fieldShouldAfter', 'messages')
                .replace('{field}', this.getLabelText())
                .replace('{otherField}', this.translate(field, 'fields', this.entityType));

            this.showValidationMessage(msg);

            return true;
        }
    }

    
    validateBefore() {
        let field = this.model.getFieldParam(this.name, 'before');

        if (!field) {
            return false;
        }

        let value = this.model.get(this.name);
        let otherValue = this.model.get(field);

        if (!(value && otherValue)) {
            return;
        }

        if (moment(value).unix() >= moment(otherValue).unix()) {
            let msg = this.translate('fieldShouldBefore', 'messages')
                .replace('{field}', this.getLabelText())
                .replace('{otherField}', this.translate(field, 'fields', this.entityType));

            this.showValidationMessage(msg);

            return true;
        }
    }
}

export default DateFieldView;
