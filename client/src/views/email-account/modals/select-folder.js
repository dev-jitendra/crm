

define('views/email-account/modals/select-folder', ['views/modal'], function (Dep) {

    return Dep.extend({

        cssName: 'select-folder-modal',

        template: 'email-account/modals/select-folder',

        data: function () {
            return {
                folders: this.options.folders,
            };
        },

        events: {
            'click [data-action="select"]': function (e) {
                var value = $(e.currentTarget).data('value');

                this.trigger('select', value);
            },
        },

        setup: function () {
            this.headerText = this.translate('Select');
        },
    });
});
