

define('views/settings/fields/pdf-engine', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = Object.keys(this.getMetadata().get(['app', 'pdfEngines']));

            if (this.params.options.length === 0) {
                this.params.options = [''];
            }
        },
    });
});
