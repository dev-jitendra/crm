

define('crm:views/opportunity/fields/contact-role', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        searchTypeList: ['anyOf', 'noneOf'],
    });
});
