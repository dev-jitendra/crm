

define('views/admin/dynamic-logic/conditions-string/group-base', ['view'], function (Dep) {

    return Dep.extend({

        template: 'admin/dynamic-logic/conditions-string/group-base',

        data: function () {
            if (!this.conditionList.length) {
                return {
                    isEmpty: true
                };
            }

            return {
                viewDataList: this.viewDataList,
                operator: this.operator,
                level: this.level
            };
        },

        setup: function () {
            this.level = this.options.level || 0;
            this.number = this.options.number || 0;
            this.scope = this.options.scope;

            this.operator = this.options.operator || this.operator;

            this.itemData = this.options.itemData || {};
            this.viewList = [];

            const conditionList = this.conditionList = this.itemData.value || [];

            this.viewDataList = [];

            conditionList.forEach((item, i) => {
                const key = 'view-' + this.level.toString() + '-' + this.number.toString() + '-' + i.toString();

                this.createItemView(i, key, item);
                this.viewDataList.push({
                    key: key,
                    isEnd: i === conditionList.length - 1,
                });
            });
        },

        getFieldType: function (item) {
            return this.getMetadata()
                .get(['entityDefs', this.scope, 'fields', item.attribute, 'type']) || 'base';
        },

        createItemView: function (number, key, item) {
            this.viewList.push(key);

            item = item || {};

            const additionalData = item.data || {};

            const type = additionalData.type || item.type || 'equals';
            const fieldType = this.getFieldType(item);

            const viewName = this.getMetadata()
                .get(['clientDefs', 'DynamicLogic', 'fieldTypes', fieldType, 'conditionTypes', type, 'itemView']) ||
                this.getMetadata()
                    .get(['clientDefs', 'DynamicLogic', 'itemTypes', type, 'view']);

            if (!viewName) {
                return;
            }

            const operator = this.getMetadata()
                .get(['clientDefs', 'DynamicLogic', 'itemTypes', type, 'operator']);

            let operatorString = this.getMetadata()
                .get(['clientDefs', 'DynamicLogic', 'itemTypes', type, 'operatorString']);

            if (!operatorString) {
                operatorString = this.getLanguage()
                    .translateOption(type, 'operators', 'DynamicLogic')
                    .toLowerCase();

                operatorString = '<i class="small">' + operatorString + '</i>';
            }

            this.createView(key, viewName, {
                itemData: item,
                scope: this.scope,
                level: this.level + 1,
                selector: '[data-view-key="'+key+'"]',
                number: number,
                operator: operator,
                operatorString: operatorString,
            });
        },
    });
});
