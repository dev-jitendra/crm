

define('views/admin/entity-manager/fields/icon-class', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        editTemplate: 'admin/entity-manager/fields/icon-class/edit',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.events['click [data-action="selectIcon"]'] = function () {
                this.selectIcon();
            };
        },

        selectIcon: function () {
            this.createView('dialog', 'views/admin/entity-manager/modals/select-icon', {}, view => {
                view.render();

                this.listenToOnce(view, 'select', value => {
                    if (value === '') {
                        value = null;
                    }

                    this.model.set(this.name, value);

                    view.close();
                });
            });
        },

        fetch: function () {
            let data = {};

            data[this.name] = this.model.get(this.name);

            return data;
        },
    });
});
