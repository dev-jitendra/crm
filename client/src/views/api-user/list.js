

define('views/api-user/list', ['views/list'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        getCreateAttributes: function () {
            return {
                type: 'api',
            };
        },
    });
});
