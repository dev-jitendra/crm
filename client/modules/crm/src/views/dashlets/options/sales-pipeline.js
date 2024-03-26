

define('crm:views/dashlets/options/sales-pipeline', ['crm:views/dashlets/options/chart'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getAcl().getLevel('Opportunity', 'read') === 'own') {
                this.hideField('team');
            }
        },
    });
});
