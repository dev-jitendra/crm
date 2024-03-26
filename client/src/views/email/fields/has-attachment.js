

define('views/email/fields/has-attachment', ['views/fields/base'], function (Dep) {

    
    return Dep.extend({

        listTemplate: 'email/fields/has-attachment/detail',
        detailTemplate: 'email/fields/has-attachment/detail',

        events: {
            'click [data-action="show"]': function (e) {
                e.stopPropagation();

                this.show();
            },
        },

        data: function () {
            let data = Dep.prototype.data.call(this);

            data.isSmall = this.mode === this.MODE_LIST;

            return data;
        },

        show: function () {
            Espo.Ui.notify(' ... ');

            this.createView('dialog', 'views/email/modals/attachments', {model: this.model})
                .then(view => {
                    view.render();

                    Espo.Ui.notify(false);
                });
        },
    });
});
