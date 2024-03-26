

define('crm:views/campaign/subscribe-again', ['view'], function (Dep) {

    return Dep.extend({

        template: 'crm:campaign/subscribe-again',

        data: function () {
            var revertUrl;

            var actionData = this.options.actionData;

            if (actionData.hash && actionData.emailAddress) {
                revertUrl = '?entryPoint=unsubscribe&emailAddress=' + actionData.emailAddress +
                    '&hash=' + actionData.hash;
            } else {
                revertUrl = '?entryPoint=unsubscribe&id=' + actionData.queueItemId;
            }

            return {
                revertUrl: revertUrl,
            };
        },
    });
});
