

define('views/scheduled-job/list', ['views/list'], function (Dep) {

    return Dep.extend({

        searchPanel: false,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.menu.buttons.push({
                link: '#Admin/jobs',
                text: this.translate('Jobs', 'labels', 'Admin'),
            });

            this.createView('search', 'views/base', {
                fullSelector: '#main > .search-container',
                template: 'scheduled-job/cronjob',
            });
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            Espo.Ajax
                .getRequest('Admin/action/cronMessage')
                .then(data => {
                    this.$el.find('.cronjob .message').html(data.message);
                    this.$el.find('.cronjob .command').html('<strong>' + data.command + '</strong>');
                });
        },

        getHeader: function () {
            return this.buildHeaderHtml([
                $('<a>')
                    .attr('href', '#Admin')
                    .text(this.translate('Administration', 'labels', 'Admin')),
                this.getLanguage().translate(this.scope, 'scopeNamesPlural')
            ]);
        },
    });
});
