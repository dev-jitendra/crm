

define('views/note/modals/edit', ['views/modals/edit'], function (Dep) {

    return Dep.extend({

        fullFormDisabled: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.once('ready', () => {
                let recordView = this.getView('edit') || this.getView('record');

                if (recordView) {
                    var fieldView = recordView.getFieldView('post');

                    if (fieldView) {
                        this.listenTo(fieldView, 'add-files', files => {
                            var attachmentsView = recordView.getFieldView('attachments');

                            if (attachmentsView) {
                                recordView.getFieldView('attachments').uploadFiles(files);
                            }
                        });
                    }
                }
            });
        },
    });
});
