

define('views/preferences/edit', ['views/edit'], function (Dep) {

    return Dep.extend({

        userName: '',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.userName = this.model.get('name');
        },

        getHeader: function () {
            return this.buildHeaderHtml([
                $('<span>').text(this.translate('Preferences')),
                $('<span>').text(this.userName),
            ]);
        },
    });
});
