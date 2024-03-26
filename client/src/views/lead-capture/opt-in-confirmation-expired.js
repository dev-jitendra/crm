

define('views/lead-capture/opt-in-confirmation-expired', ['view', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'lead-capture/opt-in-confirmation-expired',

        setup: function () {
            this.resultData = this.options.resultData;
        },

        data: function () {
            return {
                defaultMessage: this.getLanguage().translate('optInConfirmationExpired', 'messages', 'LeadCapture'),
            };
        },
    });
});
