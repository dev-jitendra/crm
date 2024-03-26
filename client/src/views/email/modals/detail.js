

define('views/email/modals/detail', ['views/modals/detail', 'views/email/detail'], function (Dep, Detail) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.addButton({
                name: 'reply',
                label: 'Reply',
                hidden: this.model && this.model.get('status') === 'Draft',
                style: 'danger',
                position: 'right',
            }, true)

            if (this.model) {
                this.listenToOnce(this.model, 'sync', () => {
                    setTimeout(() => {
                        this.model.set('isRead', true);
                    }, 50);
                });
            }
        },

        controlRecordButtonsVisibility: function () {
            Dep.prototype.controlRecordButtonsVisibility.call(this);

            if (this.model.get('status') === 'Draft' || !this.getAcl().check('Email', 'create')) {
                this.hideActionItem('reply');

                return;
            }

            this.showActionItem('reply');
        },

        actionReply: function (data, e) {
            Detail.prototype.actionReply.call(this, {}, e, this.getPreferences().get('emailReplyToAllByDefault'));
        },
    });
});
