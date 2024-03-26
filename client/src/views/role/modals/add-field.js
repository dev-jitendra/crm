

define('views/role/modals/add-field', ['views/modal'], function (Dep) {

    return Dep.extend({

        template: 'role/modals/add-field',

        events: {
            'click a[data-action="addField"]': function (e) {
                this.trigger('add-field', $(e.currentTarget).data().name);
            }
        },

        data: function () {
            var dataList = [];

            this.fieldList.forEach((field, i) => {
                if (i % 4 === 0) {
                    dataList.push([]);
                }

                dataList[dataList.length -1].push(field);
            });

            return {
                dataList: dataList,
                scope: this.scope
            };
        },

        setup: function () {
            this.headerText = this.translate('Add Field');

            var scope = this.scope = this.options.scope;
            var fields = this.getMetadata().get('entityDefs.' + scope + '.fields') || {};
            var fieldList = [];

            Object.keys(fields).forEach(field => {
                var d = fields[field];

                if (field in this.options.ignoreFieldList) {
                    return;
                }

                if (d.disabled) {
                    return;
                }

                if (
                    this.getMetadata()
                        .get(['app', this.options.type, 'mandatory', 'scopeFieldLevel', this.scope, field]) !== null
                ) {
                    return;
                }

                fieldList.push(field);
            });

            this.fieldList = this.getLanguage().sortFieldList(scope, fieldList);
        },
    });
});
