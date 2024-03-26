

define('views/working-time-calendar/fields/time-ranges/item-detail', ['view', 'lib!moment'],
(Dep, moment) => {

    
    class Class extends Dep
    {
        templateContent = `
            {{start}}
            &nbsp;–&nbsp;
            {{end}}
        `

        data() {
            return {
                start: this.convertTimeToDisplay(this.value[0]),
                end: this.convertTimeToDisplay(this.value[1]),
            };
        }

        setup() {
            this.value = this.options.value;
        }

        convertTimeToDisplay(value) {
            if (!value) {
                return '';
            }

            let m = moment(value, 'HH:mm');

            if (!m.isValid()) {
                return '';
            }

            return m.format(this.getDateTime().timeFormat);
        }
    }

    return Class;
});
