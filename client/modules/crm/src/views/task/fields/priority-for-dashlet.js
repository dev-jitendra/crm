

define('crm:views/task/fields/priority-for-dashlet', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        data: function () {
            var data = Dep.prototype.data.call(this);

            if (!data.style || data.style === 'default') {
                data.isNotEmpty = false;
            }

            return data;
        },
    });
});
