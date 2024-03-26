

define('views/admin/auth-log-record/list', ['views/list'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
        },

        getHeader: function () {
            return this.buildHeaderHtml([
                $('<a>')
                    .attr('href', '#Admin')
                    .text(this.translate('Administration')),
                $('<span>')
                    .text(this.getLanguage().translate('Auth Log', 'labels', 'Admin')),
            ]);
        },

        updatePageTitle: function () {
            this.setPageTitle(this.getLanguage().translate('Auth Log', 'labels', 'Admin'));
        },
    });
});
