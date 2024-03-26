

define('views/inbound-email/fields/folders', ['views/email-account/fields/folders'], function (Dep) {

    return Dep.extend({

        getFoldersUrl: 'InboundEmail/action/getFolders',

    });
});
