

define('views/settings/fields/time-format', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = this.getMetadata().get(['app', 'dateTime', 'timeFormatList']) || [];
        },
    });
});
