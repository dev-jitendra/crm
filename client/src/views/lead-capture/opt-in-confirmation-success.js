

define('views/lead-capture/opt-in-confirmation-success', ['view', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'lead-capture/opt-in-confirmation-success',

        setup: function () {
            let model = new Model();

            this.resultData = this.options.resultData;

            if (this.resultData.message) {
                model.set('message', this.resultData.message);

                this.createView('messageField', 'views/fields/text', {
                    selector: '.field[data-name="message"]',
                    mode: 'detail',
                    inlineEditDisabled: true,
                    model: model,
                    name: 'message',
                });
            }
        },

        data: function () {
            return {
                resultData: this.options.resultData,
                defaultMessage: this.getLanguage().translate('optInIsConfirmed', 'messages', 'LeadCapture'),
            };
        },
    });
});
