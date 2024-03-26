

define('views/dashlets/fields/records/entity-type', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.on('change', () => {
                var o = {
                    primaryFilter: null,
                    boolFilterList: [],
                    title: this.translate('Records', 'dashlets'),
                    sortBy: null,
                    sortDirection: 'asc',
                };

                o.expandedLayout = {
                    rows: []
                };

                var entityType = this.model.get('entityType');

                if (entityType) {
                    o.title = this.translate(entityType, 'scopeNamesPlural');
                    o.sortBy = this.getMetadata().get(['entityDefs', entityType, 'collection', 'orderBy']);

                    var order = this.getMetadata().get(['entityDefs', entityType, 'collection', 'order']);

                    if (order) {
                        o.sortDirection = order;
                    } else {
                        o.sortDirection = 'asc';
                    }

                    o.expandedLayout = {
                        rows: [[{name: "name", link: true, scope: entityType}]]
                    };
                }

                this.model.set(o);
            });
        },

        setupOptions: function () {
            this.params.options =  Object.keys(this.getMetadata().get('scopes'))
                .filter(scope => {
                    if (this.getMetadata().get('scopes.' + scope + '.disabled')) {
                        return;
                    }

                    if (!this.getAcl().checkScope(scope, 'read')) {
                        return;
                    }

                    if (!this.getMetadata().get(['scopes', scope, 'entity'])) {
                        return;
                    }

                    if (!this.getMetadata().get(['scopes', scope, 'object'])) {
                        return;
                    }

                    return true;
                })
                .sort((v1, v2) => {
                    return this.translate(v1, 'scopeNames').localeCompare(this.translate(v2, 'scopeNames'));
                });

            this.params.options.unshift('');
        },
    });
});
