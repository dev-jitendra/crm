

define('views/user/record/detail-side', ['views/record/detail-side'], function (Dep) {

    return Dep.extend({

        setupPanels: function () {
            Dep.prototype.setupPanels.call(this);

            if (this.model.isApi() || this.model.isSystem()) {
                this.hidePanel('activities', true);
                this.hidePanel('history', true);
                this.hidePanel('tasks', true);
                this.hidePanel('stream', true);

                return;
            }

            var showActivities = this.getAcl().checkUserPermission(this.model);

            if (!showActivities) {
                if (this.getAcl().get('userPermission') === 'team') {
                    if (!this.model.has('teamsIds')) {
                        this.listenToOnce(this.model, 'sync', function () {
                            if (this.getAcl().checkUserPermission(this.model)) {
                                this.onPanelsReady(function () {
                                    this.showPanel('activities', 'acl');
                                    this.showPanel('history', 'acl');
                                    if (!this.model.isPortal()) {
                                        this.showPanel('tasks', 'acl');
                                    }
                                });
                            }
                        }, this);
                    }
                }
            }

            if (!showActivities) {
                this.hidePanel('activities', false, 'acl');
                this.hidePanel('history', false, 'acl');
                this.hidePanel('tasks', false, 'acl');
            }

            if (this.model.isPortal()) {
                this.hidePanel('tasks', true);
            }
        },

    });
});
