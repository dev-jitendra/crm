

define('views/email-account/fields/folders', ['views/fields/array'], function (Dep) {

    return Dep.extend({

        getFoldersUrl: 'EmailAccount/action/getFolders',

        setupOptions: function () {
            this.params.options = ['INBOX'];
        },

        fetchFolders: function () {
            return new Promise(resolve => {
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

                Espo.Ajax.postRequest(this.getFoldersUrl, data)
                    .then(folders => {
                        resolve(folders);
                    })
                    .catch(xhr =>{
                        Espo.Ui.error(this.translate('couldNotConnectToImap', 'messages', 'EmailAccount'));

                        xhr.errorIsHandled = true;

                        resolve(["INBOX"]);
                    });
            });
        },

        actionAddItem: function () {
            Espo.Ui.notify(' ... ');

            this.fetchFolders()
                .then(options => {
                    Espo.Ui.notify(false);

                    this.createView( 'addModal', this.addItemModalView, {options: options})
                        .then(view => {
                            view.render();

                            view.once('add', item =>{
                                this.addValue(item);

                                view.close();
                            });

                            view.once('add-mass', items => {
                                items.forEach(item => {
                                    this.addValue(item);
                                });

                                view.close();
                            });
                        });
                });
        },
    });
});
