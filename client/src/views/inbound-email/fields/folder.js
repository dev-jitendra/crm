

define('views/inbound-email/fields/folder', ['views/email-account/fields/folder'], function (Dep) {

    return Dep.extend({

        getFoldersUrl: 'InboundEmail/action/getFolders',

    });
});
