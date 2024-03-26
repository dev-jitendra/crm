

define('views/webhook/fields/event', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            var itemList = [];

            var scopeList = this.getMetadata().getScopeObjectList();

            scopeList = scopeList.sort(function (v1, v2) {
                return v1.localeCompare(v2);
            }.bind(this));

            scopeList.forEach(function (scope) {
                itemList.push(scope + '.' + 'create');
                itemList.push(scope + '.' + 'update');
                itemList.push(scope + '.' + 'delete');
            }, this);

            this.params.options = itemList;
        },
    });
});
