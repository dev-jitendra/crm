

define('views/layout-set/fields/layout-list', [
    'views/fields/multi-enum', 'views/admin/layouts/index'], function (Dep, LayoutsIndex) {

    return Dep.extend({

        typeList: [
            'list',
            'detail',
            'listSmall',
            'detailSmall',
            'bottomPanelsDetail',
            'filters',
            'massUpdate',
            'sidePanelsDetail',
            'sidePanelsEdit',
            'sidePanelsDetailSmall',
            'sidePanelsEditSmall',
        ],

        setupOptions: function () {
            this.params.options = [];
            this.translatedOptions = {};

            this.scopeList = Object.keys(this.getMetadata().get('scopes'))
                .filter(item => {
                    return this.getMetadata().get(['scopes', item, 'layouts']);
                })
                .sort((v1, v2) => {
                    return this.translate(v1, 'scopeNames')
                        .localeCompare(this.translate(v2, 'scopeNames'));
                });

            let dataList = LayoutsIndex.prototype.getLayoutScopeDataList.call(this);

            dataList.forEach(item1 => {
                item1.typeList.forEach(type => {
                    let item = item1.scope + '.' + type;

                    if (type.substr(-6) === 'Portal') {
                        return;
                    }

                    this.params.options.push(item);

                    this.translatedOptions[item] = this.translate(item1.scope, 'scopeNames') + '.' +
                        this.translate(type, 'layouts', 'Admin');
                });
            });
        },

        translateLayoutName: function (type, scope) {
            return LayoutsIndex.prototype.translateLayoutName.call(this, type, scope);
        },
    });
});
