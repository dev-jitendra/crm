

define('crm:views/mass-email/fields/smtp-account', ['views/lead-capture/fields/smtp-account'], function (Dep) {

    return Dep.extend({

        dataUrl: 'MassEmail/action/smtpAccountDataList',
    });
});
