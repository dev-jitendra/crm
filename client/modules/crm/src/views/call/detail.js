

define('crm:views/call/detail', ['views/detail', 'crm:views/meeting/detail'], function (Dep, MeetingDetail) {

    return Dep.extend({

        cancellationPeriod: '8 hours',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.controlSendInvitationsButton();
            this.controlAcceptanceStatusButton();
            this.controlSendCancellationButton();

            this.listenTo(this.model, 'sync', () => {
                this.controlSendInvitationsButton();
                this.controlSendCancellationButton();
            });

            this.listenTo(this.model, 'sync', () => {
                this.controlAcceptanceStatusButton();
            });

            MeetingDetail.prototype.setupCancellationPeriod.call(this);
        },

        actionSendInvitations: function () {
            MeetingDetail.prototype.actionSendInvitations.call(this);
        },

        actionSendCancellation: function () {
            MeetingDetail.prototype.actionSendCancellation.call(this);
        },

        actionSetAcceptanceStatus: function () {
            MeetingDetail.prototype.actionSetAcceptanceStatus.call(this);
        },

        controlSendInvitationsButton: function () {
            MeetingDetail.prototype.controlSendInvitationsButton.call(this);
        },

        controlSendCancellationButton: function () {
            MeetingDetail.prototype.controlSendCancellationButton.call(this);
        },

        controlAcceptanceStatusButton: function () {
            MeetingDetail.prototype.controlAcceptanceStatusButton.call(this);
        },
    });
});
