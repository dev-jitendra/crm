

define('views/admin/link-manager/fields/foreign-link-entity-type-list', ['views/fields/checklist'], function (Dep) {

    return Dep.extend({

        setup: function () {
            this.params.translation = 'Global.scopeNames';

            Dep.prototype.setup.call(this);
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            this.controlOptionsAvailability();
        },

        controlOptionsAvailability: function () {
            this.params.options.forEach(item => {
                var link = this.model.get('link');
                var linkForeign = this.model.get('linkForeign');
                var entityType = this.model.get('entity');

                var linkDefs = this.getMetadata().get(['entityDefs', item, 'links']) || {};

                var isFound = false;

                for (let i in linkDefs) {
                    if (
                        linkDefs[i].foreign === link &&
                        !linkDefs[i].isCustom &&
                        linkDefs[i].entity === entityType
                    ) {
                        isFound = true;
                    } else if (i === linkForeign && linkDefs[i].type !== 'hasChildren') {
                        isFound = true;
                    }
                }

                if (isFound) {
                    this.$el
                        .find('input[data-name="checklistItem-foreignLinkEntityTypeList-'+item+'"]')
                        .attr('disabled', 'disabled');
                }
            });
        },
    });
});
