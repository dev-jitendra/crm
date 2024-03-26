

define('views/admin/label-manager/category', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/label-manager/category',

        data: function () {
            return {
                categoryDataList: this.getCategoryDataList()
            };
        },

        events: {},

        setup: function () {
            this.scope = this.options.scope;
            this.language = this.options.language;
            this.categoryData = this.options.categoryData;
        },

        getCategoryDataList: function () {
            var labelList = Object.keys(this.categoryData);

            labelList.sort((v1, v2) => {
                return v1.localeCompare(v2);
            });

            var categoryDataList = [];

            labelList.forEach(name => {
                var value = this.categoryData[name];

                if (value === null) {
                    value = '';
                }

                if (value.replace) {
                    value = value.replace(/\n/i, '\\n');
                }

                var o = {
                    name: name,
                    value: value,
                };

                var arr = name.split('[.]');

                o.label = arr.slice(1).join(' . ');

                categoryDataList.push(o);
            });

            return categoryDataList;
        },
    });
});
