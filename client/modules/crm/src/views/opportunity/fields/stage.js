

define('crm:views/opportunity/fields/stage', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.probabilityMap = this.getMetadata().get('entityDefs.Opportunity.fields.stage.probabilityMap') || {};

            if (this.mode !== 'list') {
                this.on('change', () => {
                    var probability = this.probabilityMap[this.model.get(this.name)];

                    if (probability !== null && probability !== undefined) {
                        this.model.set('probability', probability);
                    }
                });
            }
        },
    });
});
