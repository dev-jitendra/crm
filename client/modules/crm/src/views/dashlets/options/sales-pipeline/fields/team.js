

define('crm:views/dashlets/options/sales-pipeline/fields/team', ['views/fields/link'], function (Dep) {

    return Dep.extend({

        getSelectBoolFilterList: function () {
            if (this.getAcl().getLevel('Opportunity', 'read') === 'team') {
                return ['onlyMy'];
            }
        },
    });

});
