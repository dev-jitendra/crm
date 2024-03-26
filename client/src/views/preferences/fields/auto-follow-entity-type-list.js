

define('views/preferences/fields/auto-follow-entity-type-list', ['views/fields/multi-enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            this.params.options = Object.keys(this.getMetadata().get('scopes'))
                .filter(scope => {
                    if (this.getMetadata().get('scopes.' + scope + '.disabled')) {
                        return;
                    }

                    return this.getMetadata().get('scopes.' + scope + '.entity') &&
                        this.getMetadata().get('scopes.' + scope + '.stream');
                })
                .sort((v1, v2) => {
                    return this.translate(v1, 'scopeNamesPlural')
                        .localeCompare(this.translate(v2, 'scopeNamesPlural'));
                });

            Dep.prototype.setup.call(this);
        },
    });
});
