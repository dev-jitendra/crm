

define('views/settings/fields/global-search-entity-list', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        setup: function () {

            this.params.options = Object.keys(this.getMetadata().get('scopes'))
                .filter(scope => {
                    let defs = this.getMetadata().get(['scopes', scope]) || {};

                    if (defs.disabled || scope === 'Note') {
                        return;
                    }

                    return defs.customizable && defs.entity;
                })
                .sort((v1, v2) => {
                    return this.translate(v1, 'scopeNamesPlural')
                        .localeCompare(this.translate(v2, 'scopeNamesPlural'));
                });

            Dep.prototype.setup.call(this);
        },
    });
});
