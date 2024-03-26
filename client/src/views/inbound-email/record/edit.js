

define('views/inbound-email/record/edit', ['views/record/edit', 'views/inbound-email/record/detail'],
function (Dep, Detail) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            Detail.prototype.setupFieldsBehaviour.call(this);
            Detail.prototype.initSslFieldListening.call(this);

            if (Detail.prototype.wasFetched.call(this)) {
                this.setFieldReadOnly('fetchSince');
            }
        },

        modifyDetailLayout: function (layout) {
            Detail.prototype.modifyDetailLayout.call(this, layout);
        },

        controlStatusField: function () {
            Detail.prototype.controlStatusField.call(this);
        },

        initSmtpFieldsControl: function () {
            Detail.prototype.initSmtpFieldsControl.call(this);
        },

        controlSmtpFields: function () {
            Detail.prototype.controlSmtpFields.call(this);
        },

        controlSentFolderField: function () {
            Detail.prototype.controlSentFolderField.call(this);
        },

        controlSmtpAuthField: function () {
            Detail.prototype.controlSmtpAuthField.call(this);
        },

        wasFetched: function () {
            Detail.prototype.wasFetched.call(this);
        },
    });
});
