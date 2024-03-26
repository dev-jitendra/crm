

define('views/admin/layouts/record/edit-attributes', ['views/record/base'], function (Dep) {

    return Dep.extend({

        template: 'admin/layouts/record/edit-attributes',

        
        mode: 'edit',

        data: function () {
            return {
                attributeDataList: this.getAttributeDataList()
            };
        },

        getAttributeDataList: function () {
            const list = [];

            this.attributeList.forEach(attribute => {
                const defs = this.attributeDefs[attribute] || {};

                const type = defs.type;

                const isWide = !['enum', 'bool', 'int', 'float', 'varchar'].includes(type) &&
                    attribute !== 'widthComplex';

                list.push({
                    name: attribute,
                    viewKey: attribute + 'Field',
                    isWide: isWide,
                    label: this.translate(defs.label || attribute, 'fields', 'LayoutManager'),
                });
            });

            return list;
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.attributeList = this.options.attributeList || [];
            this.attributeDefs = this.options.attributeDefs || {};

            this.attributeList.forEach(field => {
                const params = this.attributeDefs[field] || {};
                const type = params.type || 'base';

                const viewName = params.view || this.getFieldManager().getViewName(type);

                this.createField(field, viewName, params);
            });
        },
    });
});
