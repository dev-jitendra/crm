

define('views/site-portal/master', ['views/site/master'], function (Dep) {

    return Dep.extend({

        template: 'site/master',

        views: {
            header: {
                id: 'header',
                view: 'views/site-portal/header'
            },
            main: {
                id: 'main',
                view: false,
            },
            footer: {
                fullSelector: 'body > footer',
                view: 'views/site/footer'
            }
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);
            this.$el.find('#main').addClass('main-portal');
        },

    });
});
