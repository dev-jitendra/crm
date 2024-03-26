

define('views/dashlets/fields/records/sort-direction', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:entityType', () => {
                this.setupOptions();
                this.reRender();
            });
        },
    });
});
