

define('views/preferences/fields/theme', ['views/settings/fields/theme'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Object.keys(this.getMetadata().get('themes') || {})
                .sort((v1, v2) => {
                    if (v2 === 'EspoRtl') {
                        return -1;
                    }

                    return this.translate(v1, 'themes').localeCompare(this.translate(v2, 'themes'));
                });

            this.params.options.unshift('');
        },

        setupTranslation: function () {
            Dep.prototype.setupTranslation.call(this);

            this.translatedOptions = this.translatedOptions || {};

            let defaultTheme = this.getConfig().get('theme');
            let defaultTranslated = this.translatedOptions[defaultTheme] || defaultTheme;

            this.translatedOptions[''] = this.translate('Default') + ' (' + defaultTranslated + ')';
        },

        afterRenderDetail: function () {
            let navbar = this.getNavbarValue() || this.getDefaultNavbar();

            if (navbar) {
                this.$el
                    .append(' ')
                    .append(
                        $('<span>').addClass('text-muted chevron-right')
                    )
                    .append(' ')
                    .append(
                        $('<span>').text(this.translate(navbar, 'themeNavbars'))
                    )
            }
        },
    });
});
