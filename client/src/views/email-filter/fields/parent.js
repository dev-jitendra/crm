

define('views/email-filter/fields/parent', ['views/fields/link-parent'], function (Dep) {

    return Dep.extend({

        getSelectPrimaryFilterName: function () {
            var map = {
                'User': 'active',
            };

            if (!this.foreignScope) {
                return;
            }

            return map[this.foreignScope];
        },
    });
});
