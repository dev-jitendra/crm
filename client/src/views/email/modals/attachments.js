

define('views/email/modals/attachments', ['views/modal'], function (Dep) {

    
    return Dep.extend({

        backdrop: true,

        templateContent: `<div class="record">{{{record}}}</div>`,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.headerText = this.translate('attachments', 'fields', 'Email');

            this.createView('record', 'views/record/detail', {
                model: this.model,
                selector: '.record',
                readOnly: true,
                sideView: null,
                buttonsDisabled: true,
                detailLayout: [
                    {
                        rows: [
                            [
                                {
                                    name: 'attachments',
                                    noLabel: true,
                                },
                                false,
                            ]
                        ]
                    }
                ],
            });

            if (!this.model.has('attachmentsIds')) {
                this.wait(
                    this.model.fetch()
                );
            }
        },
    });
});
