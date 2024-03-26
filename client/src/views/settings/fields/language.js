

define('views/settings/fields/language', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Espo.Utils.clone(this.getMetadata().get(['app', 'language', 'list']) || []);
            this.translatedOptions = Espo.Utils.clone(this.getLanguage().translate('language', 'options') || {});
        },
    });
});
