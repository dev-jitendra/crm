

define('views/email/modals/body-plain', ['views/modal'], function (Dep) {

    return Dep.extend({

        backdrop: true,

        templateContent: '<div class="field" data-name="body-plain">{{{bodyPlain}}}</div>',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.buttonList.push({
                'name': 'cancel',
                'label': 'Close'
            });

            this.headerText = this.model.get('name');

            this.createView('bodyPlain', 'views/fields/text', {
                selector: '.field[data-name="bodyPlain"]',
                model: this.model,
                defs: {
                    name: 'bodyPlain',
                    params: {
                        readOnly: true,
                        inlineEditDisabled: true,
                    },
                },
            });
        },
    });
});
