

define('views/email/fields/select-template', ['views/fields/link'], function (Dep) {

    return Dep.extend({

        type: 'link',

        foreignScope: 'EmailTemplate',

        editTemplate: 'email/fields/select-template/edit',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.on('change', () => {
                let id = this.model.get(this.idName);

                if (id) {
                    this.loadTemplate(id);
                }
            });
        },

        getSelectPrimaryFilterName: function () {
            return 'actual';
        },

        loadTemplate: function (id) {
            let to = this.model.get('to') || '';
            let emailAddress = null;

            to = to.trim();

            if (to) {
                emailAddress = to.split(';')[0].trim();
            }

            Espo.Ajax
                .postRequest(`EmailTemplate/${id}/prepare`, {
                    emailAddress: emailAddress,
                    parentType: this.model.get('parentType'),
                    parentId: this.model.get('parentId'),
                    relatedType: this.model.get('relatedType'),
                    relatedId: this.model.get('relatedId'),
                })
                .then(data => {
                    this.model.trigger('insert-template', data);

                    this.emptyField();
                })
                .catch(() => {
                    this.emptyField();
                });
        },

        emptyField: function () {
            this.model.set(this.idName, null);
            this.model.set(this.nameName, '');
        },
    });
});
