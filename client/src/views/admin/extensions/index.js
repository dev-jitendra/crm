

import Dep from 'view';
import SelectProvider from 'helpers/list/select-provider';

export default Dep.extend({

    template: 'admin/extensions/index',

    packageContents: null,

    events: {
        'change input[name="package"]': function (e) {
            this.$el.find('button[data-action="upload"]')
                .addClass('disabled')
                .attr('disabled', 'disabled');

            this.$el.find('.message-container').html('');

            const files = e.currentTarget.files;

            if (files.length) {
                this.selectFile(files[0]);
            }
        },
        'click button[data-action="upload"]': function () {
            this.upload();
        },
        'click [data-action="install"]': function (e) {
            const id = $(e.currentTarget).data('id');

            const name = this.collection.get(id).get('name');
            const version = this.collection.get(id).get('version');

            this.run(id, name, version);

        },
        'click [data-action="uninstall"]': function (e) {
            const id = $(e.currentTarget).data('id');

            this.confirm(this.translate('uninstallConfirmation', 'messages', 'Admin'), () => {
                Espo.Ui.notify(this.translate('Uninstalling...', 'labels', 'Admin'));

                Espo.Ajax
                    .postRequest('Extension/action/uninstall', {id: id}, {timeout: 0, bypassAppReload: true})
                    .then(() => {
                        Espo.Ui.success(this.translate('Done'));

                        setTimeout(() => window.location.reload(), 500);
                    })
                    .catch(xhr => {
                        const msg = xhr.getResponseHeader('X-Status-Reason');

                        this.showErrorNotification(this.translate('Error') + ': ' + msg);
                    });
            });
        }
    },

    setup: function () {
        const selectProvider = new SelectProvider(
            this.getHelper().layoutManager,
            this.getHelper().metadata,
            this.getHelper().fieldManager
        );

        this.wait(
            this.getCollectionFactory()
                .create('Extension')
                .then(collection => {
                    this.collection = collection;
                    this.collection.maxSize = this.getConfig().get('recordsPerPage');
                })
                .then(() => selectProvider.get('Extension'))
                .then(select => {
                    this.collection.data.select = select.join(',');
                })
                .then(() => this.collection.fetch())
                .then(() => {
                    this.createView('list', 'views/extension/record/list', {
                        collection: this.collection,
                        selector: '> .list-container',
                    });

                    if (this.collection.length === 0) {
                        this.once('after:render', () => {
                            this.$el.find('.list-container').addClass('hidden');
                        });
                    }
                })
        );
    },

    selectFile: function (file) {
        const fileReader = new FileReader();

        fileReader.onload = (e) => {
            this.packageContents = e.target.result;

            this.$el.find('button[data-action="upload"]')
                .removeClass('disabled')
                .removeAttr('disabled');
        };

        fileReader.readAsDataURL(file);
    },

    showError: function (msg) {
        msg = this.translate(msg, 'errors', 'Admin');

        this.$el.find('.message-container').html(msg);
    },

    showErrorNotification: function (msg) {
        if (!msg) {
            this.$el.find('.notify-text').addClass('hidden');

            return;
        }

        msg = this.translate(msg, 'errors', 'Admin');

        this.$el.find('.notify-text').html(msg);
        this.$el.find('.notify-text').removeClass('hidden');
    },

    upload: function () {
        this.$el.find('button[data-action="upload"]').addClass('disabled').attr('disabled', 'disabled');

        this.notify('Uploading...');

        Espo.Ajax
            .postRequest('Extension/action/upload', this.packageContents, {
                timeout: 0,
                contentType: 'application/zip',
            })
            .then(data => {
                if (!data.id) {
                    this.showError(this.translate('Error occurred'));

                    return;
                }

                Espo.Ui.notify(false);

                this.createView('popup', 'views/admin/extensions/ready', {
                    upgradeData: data,
                }, view => {
                    view.render();

                    this.$el.find('button[data-action="upload"]')
                        .removeClass('disabled')
                        .removeAttr('disabled');

                    view.once('run', () => {
                        view.close();

                        this.$el.find('.panel.upload').addClass('hidden');

                        this.run(data.id, data.version, data.name);
                    });
                });
            })
            .catch(xhr => {
                this.showError(xhr.getResponseHeader('X-Status-Reason'));

                Espo.Ui.notify(false);
            });
    },

    run: function (id, version, name) {
        Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

        this.showError(false);
        this.showErrorNotification(false);

        Espo.Ajax
            .postRequest('Extension/action/install', {id: id}, {timeout: 0, bypassAppReload: true})
            .then(() => {
                const cache = this.getCache();

                if (cache) {
                    cache.clear();
                }

                this.createView('popup', 'views/admin/extensions/done', {
                    version: version,
                    name: name,
                }, view => {
                    if (this.collection.length) {
                        this.collection.fetch({bypassAppReload: true});
                    }

                    this.$el.find('.list-container').removeClass('hidden');
                    this.$el.find('.panel.upload').removeClass('hidden');

                    Espo.Ui.notify(false);

                    view.render();
                });
            })
            .catch(xhr => {
                this.$el.find('.panel.upload').removeClass('hidden');

                const msg = xhr.getResponseHeader('X-Status-Reason');

                this.showErrorNotification(this.translate('Error') + ': ' + msg);
            });
    },
});
