

define('crm:views/campaign/unsubscribe', ['view'], function (Dep) {

    return Dep.extend({

        template: 'crm:campaign/unsubscribe',

        data: function () {
            var revertUrl;

            var actionData = this.options.actionData;

            revertUrl = actionData.hash && actionData.emailAddress ?
                '?entryPoint=subscribeAgain&emailAddress=' + actionData.emailAddress + '&hash=' + actionData.hash :
                '?entryPoint=subscribeAgain&id=' + actionData.queueItemId;

            return {
                revertUrl: revertUrl,
            };
        },
    });
});
