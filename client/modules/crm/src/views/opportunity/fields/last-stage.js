

define('crm:views/opportunity/fields/last-stage', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            var optionList = this.getMetadata().get('entityDefs.Opportunity.fields.stage.options', []);
            var probabilityMap = this.getMetadata().get('entityDefs.Opportunity.fields.stage.probabilityMap', {});

            this.params.options = [];

            optionList.forEach(item => {
                if (!probabilityMap[item]) {
                    return;
                }

                if (probabilityMap[item] === 100) {
                    return;
                }

                this.params.options.push(item);
            });

            this.params.translation = 'Opportunity.options.stage';

            Dep.prototype.setup.call(this);
        },
    });
});
