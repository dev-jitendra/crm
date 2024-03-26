

define('views/user/record/panels/stream', ['views/stream/panel'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            let assignmentPermission = this.getAcl().checkPermission('message', this.model);

            if (this.model.id === this.getUser().id) {
                this.placeholderText = this.translate('writeMessageToSelf', 'messages');
            } else {
                this.placeholderText = this.translate('writeMessageToUser', 'messages')
                    .replace('{user}', this.model.get('name'));
            }

            if (!assignmentPermission) {
                this.postDisabled = true;

                if (this.getAcl().getPermissionLevel('message') === 'team') {
                    if (!this.model.has('teamsIds')) {
                        this.listenToOnce(this.model, 'sync', () => {
                            assignmentPermission = this.getAcl().checkUserPermission(this.model);

                            if (assignmentPermission) {
                                this.postDisabled = false;
                                this.$el.find('.post-container').removeClass('hidden');
                            }
                        });
                    }
                }
            }
        },

        prepareNoteForPost: function (model) {
            var userIdList = [this.model.id];
            var userNames = {};

            userNames[userIdList] = this.model.get('name');

            model.set('usersIds', userIdList);
            model.set('usersNames', userNames);
            model.set('targetType', 'users');
        },
    });
});
