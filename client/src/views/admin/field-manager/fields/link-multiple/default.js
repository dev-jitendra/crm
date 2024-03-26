

define('views/admin/field-manager/fields/link-multiple/default', ['views/fields/link-multiple'], function (Dep) {

    return Dep.extend({

        data: function () {
            var defaultAttributes = this.model.get('defaultAttributes') || {};

            var nameHash = defaultAttributes[this.options.field + 'Names'] || {};
            var idValues = defaultAttributes[this.options.field + 'Ids'] || [];

            var data = Dep.prototype.data.call(this);

            data.nameHash = nameHash;
            data.idValues = idValues;

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.foreignScope = this.getMetadata()
                .get(['entityDefs', this.options.scope, 'links', this.options.field, 'entity']);
        },

        fetch: function () {
            var data = Dep.prototype.fetch.call(this);

            var defaultAttributes = {};

            defaultAttributes[this.options.field + 'Ids'] = data[this.idsName];
            defaultAttributes[this.options.field + 'Names'] = data[this.nameHashName];

            if (data[this.idsName] === null || data[this.idsName].length === 0) {
                defaultAttributes = null;
            }

            return {
                defaultAttributes: defaultAttributes
            };
        },

        copyValuesFromModel: function () {
            var defaultAttributes = this.model.get('defaultAttributes') || {};

            var idValues = defaultAttributes[this.options.field + 'Ids'] || [];
            var nameHash = defaultAttributes[this.options.field + 'Names'] || {};

            this.ids = idValues;
            this.nameHash = nameHash;
        },
    });
});
