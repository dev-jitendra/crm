

define('views/email-account/list', ['views/list'], function (Dep) {

    return Dep.extend({

        keepCurrentRootUrl: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.options.params = this.options.params || {};

            var params = this.options.params || {};
            if (params.userId) {
                this.collection.where = [{
                    type: 'equals',
                    field: 'assignedUserId',
                    value: params.userId
                }];
            }
        },

        getCreateAttributes: function () {
            var attributes = {};
            if (this.options.params.userId) {
                attributes.assignedUserId = this.options.params.userId;
                attributes.assignedUserName = this.options.params.userName || this.options.params.userId;
            }
            return attributes;
        },

    });
});
