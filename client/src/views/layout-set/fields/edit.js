

define('views/layout-set/fields/edit', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        detailTemplateContent:
            "<a class=\"btn btn-default\" href=\"#LayoutSet/editLayouts/id={{model.id}}\">" +
            "{{translate 'Edit Layouts' scope='LayoutSet'}}</a>",

        editTemplateContent: '',

    });
});
