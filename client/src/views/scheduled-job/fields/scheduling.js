

define('views/scheduled-job/fields/scheduling', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.isEditMode() || this.isDetailMode()) {
                this.wait(
                    Espo.loader.requirePromise('lib!cronstrue')
                        .then(Cronstrue => {
                            this.Cronstrue = Cronstrue;

                            this.listenTo(this.model, 'change:' + this.name, () => this.showText());
                        })
                );
            }
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.isEditMode() || this.isDetailMode()) {
                let $text = this.$text = $('<div class="small text-success"/>');

                this.$el.append($text);
                this.showText();
            }
        },

        showText: function () {
            if (!this.$text || !this.$text.length) {
                return;
            }

            if (!this.Cronstrue) {
                return;
            }

            var exp = this.model.get(this.name);

            if (!exp) {
                this.$text.text('');

                return;
            }

            if (exp === '* * * * *') {
                this.$text.text(this.translate('As often as possible', 'labels', 'ScheduledJob'));

                return;
            }

            var locale = 'en';
            var localeList = Object.keys(this.Cronstrue.default.locales);
            var language = this.getLanguage().name;

            if (~localeList.indexOf(language)) {
                locale = language;
            }
            else if (~localeList.indexOf(language.split('_')[0])) {
                locale = language.split('_')[0];
            }

            try {
                var text = this.Cronstrue.toString(exp, {
                    use24HourTimeFormat: !this.getDateTime().hasMeridian(),
                    locale: locale,
                });

            }
            catch (e) {
                text = this.translate('Not valid');
            }

            this.$text.text(text);
        },
    });
});
