

define('views/export/modals/idle', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        className: 'dialog dialog-record',

        template: 'export/modals/idle',

        checkInterval: 4000,

        data: function () {
            return {
                infoText: this.translate('infoText', 'messages', 'Export'),
            };
        },

        events: {
            'click [data-action="download"]': function () {
                this.actionDownload();
            },
        },

        setup: function () {
            this.action = this.options.action;
            this.id = this.options.id;
            this.status = 'Pending';

            this.headerText = this.translate('Export');

            this.model = new Model();
            this.model.name = 'Export';

            this.model.setDefs({
                fields: {
                    'status': {
                        type: 'enum',
                        readOnly: true,
                        options: [
                            'Pending',
                            'Running',
                            'Success',
                            'Failed',
                        ],
                        style: {
                            'Success': 'success',
                            'Failed': 'danger',
                        },
                    },
                    'attachmentId': {
                        type: 'varchar',
                    },
                }
            });

            this.model.set({
                status: this.status,
                processedCount: null,
            });

            this.createView('record', 'views/record/edit-for-modal', {
                scope: 'None',
                model: this.model,
                selector: '.record',
                detailLayout: [
                    {
                        rows: [
                            [
                                {
                                    name: 'status',
                                    labelText: this.translate('status', 'fields', 'Export'),
                                }
                            ]
                        ]
                    }
                ],
            });

            this.on('close', () => {
                let status = this.model.get('status');

                if (
                    status !== 'Pending' &&
                    status !== 'Running'
                ) {
                    return;
                }

                Espo.Ajax.postRequest(`Export/${this.id}/subscribe`);
            });

            this.checkStatus();
        },

        checkStatus: function () {
            Espo.Ajax
                .getRequest(`Export/${this.id}/status`)
                .then(response => {
                    let status = response.status;

                    this.model.set('status', status);

                    if (status === 'Pending' || status === 'Running') {
                        setTimeout(() => this.checkStatus(), this.checkInterval);

                        return;
                    }

                    this.model.set({
                        attachmentId: response.attachmentId,
                    });

                    if (status === 'Success') {
                        this.trigger('success', {
                            attachmentId: response.attachmentId,
                        });

                        this.showDownload();
                    }

                    if (this.$el) {
                        this.$el.find('.info-text').addClass('hidden');
                    }
                });
        },

        showDownload: function () {
            this.$el.find('.download-container').removeClass('hidden');

            let $download = this.$el.find('[data-action="download"]');

            $download.removeClass('hidden');
        },

        actionDownload: function () {
            this.trigger('download', this.model.get('attachmentId'));

            this.close();
        },
    });
});
