

define('views/preferences/fields/language', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options =
                Espo.Utils.clone(this.getMetadata().get(['app', 'language', 'list']) || [])
                    .sort((v1, v2) => {
                        return this.getLanguage().translateOption(v1, 'language')
                            .localeCompare(this.getLanguage().translateOption(v2, 'language'));
                    });

            this.params.options.unshift('');

            this.translatedOptions = Espo.Utils.clone(this.getLanguage().translate('language', 'options') || {});

            var defaultTranslated =  this.translatedOptions[this.getConfig().get('language')] || this.getConfig().get('language');

            this.translatedOptions[''] = this.translate('Default') + ' (' + defaultTranslated + ')';
        },
    });
});
