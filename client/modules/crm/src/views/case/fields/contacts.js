

define('crm:views/case/fields/contacts', ['views/fields/link-multiple-with-primary'], function (Dep) {

    return Dep.extend({

        primaryLink: 'contact',
    });
});
