

define('views/admin/dynamic-logic/conditions/not', ['views/admin/dynamic-logic/conditions/group-base'], function (Dep) {

    return Dep.extend({

        template: 'admin/dynamic-logic/conditions/not',

        operator: 'not',

        data: function () {
            return {
                viewKey: this.viewKey,
                operator: this.operator,
                hasItem: this.hasView(this.viewKey),
                level: this.level,
                groupOperator: this.getGroupOperator(),
            };
        },

        setup: function () {
            this.level = this.options.level || 0;
            this.number = this.options.number || 0;
            this.scope = this.options.scope;

            this.itemData = this.options.itemData || {};
            this.viewList = [];

            const i = 0;
            const key = this.getKey();

            this.createItemView(i, key, this.itemData.value);
            this.viewKey = key;
        },

        removeItem: function () {
            const key = this.getKey();

            this.clearView(key);

            this.controlAddItemVisibility();
        },

        getKey: function () {
            const i = 0;

            return 'view-' + this.level.toString() + '-' + this.number.toString() + '-' + i.toString();
        },

        getIndexForNewItem: function () {
            return 0;
        },

        addItemContainer: function () {},

        addViewDataListItem: function () {},

        fetch: function () {
            const view = this.getView(this.viewKey);

            if (!view) {
                return {
                    type: 'and',
                    value: [],
                };
            }

            const value = view.fetch();

            return {
                type: this.operator,
                value: value,
            };
        },

        controlAddItemVisibility: function () {
            if (this.getView(this.getKey())) {
                this.$el.find(' > .group-bottom').addClass('hidden');
            } else {
                this.$el.find(' > .group-bottom').removeClass('hidden');
            }
        },
    });
});
