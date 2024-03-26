

define('views/email-account/fields/folder', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        editTemplate: 'email-account/fields/folder/edit',

        getFoldersUrl: 'EmailAccount/action/getFolders',

        events: {
            'click [data-action="selectFolder"]': function () {
                Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

                var data = {
                    host: this.model.get('host'),
                    port: this.model.get('port'),
                    security: this.model.get('security'),
                    username: this.model.get('username'),
                    emailAddress: this.model.get('emailAddress'),
                    userId: this.model.get('assignedUserId'),
                };

                if (this.model.has('password')) {
                    data.password = this.model.get('password');
                }

                if (!this.model.isNew()) {
                    data.id = this.model.id;
                }

                Espo.Ajax.postRequest(this.getFoldersUrl, data).then(folders => {
                    this.createView('modal', 'views/email-account/modals/select-folder', {
                        folders: folders
                    }, view => {
                        Espo.Ui.notify(false);

                        view.render();

                        this.listenToOnce(view, 'select', (folder) => {
                            view.close();

                            this.addFolder(folder);
                        });
                    });
                })
                .catch(xhr => {
                    Espo.Ui.error(this.translate('couldNotConnectToImap', 'messages', 'EmailAccount'));

                    xhr.errorIsHandled = true;
                });
            }
        },

        addFolder: function (folder) {
            this.$element.val(folder);
        },
    });
});
