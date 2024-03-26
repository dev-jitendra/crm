

define('views/settings/fields/currency-list', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        matchAnyWord: true,

        setupOptions: function () {
            this.params.options = this.getMetadata().get(['app', 'currency', 'list']) || [];
            this.translatedOptions = {};

            this.params.options.forEach(item => {
                var value = item

                var name = this.getLanguage().get('Currency', 'names', item);

                if (name) {
                    value += ' - ' + name;
                }

                this.translatedOptions[item] = value;
            });
        },
    });
});
