

define('views/admin/upgrade/done', ['views/modal'], function (Dep) {

    return Dep.extend({

        cssName: 'done-modal',
        header: false,
        createButton: true,

        template: 'admin/upgrade/done',

        data: function () {
            return {
                version: this.options.version,
                text: this.translate('upgradeDone', 'messages', 'Admin').replace('{version}', this.options.version),
            };
        },

        setup: function () {
            this.on('remove', () => {
                window.location.reload();
            });

            this.buttonList = [
                {
                    name: 'close',
                    label: 'Close',
                    onClick: (dialog) => {
                        setTimeout(() => {
                            this.getRouter().navigate('#Admin', {trigger: true});
                        }, 500);

                        dialog.close();
                    },
                }
            ];

            this.header = this.getLanguage().translate('Upgraded successfully', 'labels', 'Admin');
        },
    });
});
