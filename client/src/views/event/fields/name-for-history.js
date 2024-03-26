

define('views/event/fields/name-for-history', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        listLinkTemplate: 'event/fields/name-for-history/list-link',

        data: function () {
            let data = Dep.prototype.data.call(this);

            let status = this.model.get('status');

            let canceledStatusList = this.getMetadata()
                .get(['scopes', this.model.entityType, 'canceledStatusList']) || [];

            data.strikethrough = canceledStatusList.includes(status);

            return data;
        },
    });
});
