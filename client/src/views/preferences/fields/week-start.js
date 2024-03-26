

define('views/preferences/fields/week-start', ['views/fields/enum-int'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(this.params.options);

            this.params.options.unshift(-1);

            this.translatedOptions = {};

            var dayList = this.getLanguage().get('Global', 'lists', 'dayNames') || [];

            dayList.forEach((item, i) => {
                this.translatedOptions[i] = item;
            });

            var defaultWeekStart = this.getConfig().get('weekStart');

            this.translatedOptions[-1] = this.translate('Default') +
                ' (' + dayList[defaultWeekStart] +
                ')';
        },
    });
});
