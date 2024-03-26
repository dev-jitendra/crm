

define('crm:views/lead/detail', ['views/detail'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.addMenuItem('buttons', {
                name: 'convert',
                action: 'convert',
                label: 'Convert',
                acl: 'edit',
                hidden: !this.isConvertable(),
                onClick: () => this.actionConvert(),
            });

            if (!this.model.has('status')) {
                this.listenToOnce(this.model, 'sync', () => {
                    if (this.isConvertable()) {
                        this.showHeaderActionItem('convert');
                    }
                });
            }
        },

        isConvertable: function () {
            return !['Converted', 'Dead'].includes(this.model.get('status')) ||
                !this.model.has('status');
        },

        actionConvert: function () {
            this.getRouter().navigate(this.model.entityType + '/convert/' + this.model.id , {trigger: true});
        },
    });
});
