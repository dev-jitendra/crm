

import NoteStreamView from 'views/stream/note';

class PostNoteStreamView extends NoteStreamView {

    template = 'stream/notes/post'
    messageName = 'post'
    isEditable = true
    isRemovable = true

    data() {
        let data = super.data();

        data.showAttachments = !!(this.model.get('attachmentsIds') || []).length;
        data.showPost = !!this.model.get('post');
        data.isInternal = this.isInternal;

        return data;
    }

    setup() {
        this.createField('post', null, null, 'views/stream/fields/post');

        this.createField('attachments', 'attachmentMultiple', {}, 'views/stream/fields/attachment-multiple', {
            previewSize: this.options.isNotification ? 'small' : 'medium'
        });

        this.isInternal = this.model.get('isInternal');

        if (!this.model.get('post') && this.model.get('parentId')) {
            this.messageName = 'attach';

            if (this.isThis) {
                this.messageName += 'This';
            }
        }

        this.listenTo(this.model, 'change', () => {
            if (this.model.hasChanged('post') || this.model.hasChanged('attachmentsIds')) {
                this.reRender();
            }
        });

        if (this.model.get('parentId')) {
            this.createMessage();

            return;
        }

        if (this.model.get('isGlobal')) {
            this.messageName = 'postTargetAll';
            this.createMessage();

            return;
        }

        if (this.model.has('teamsIds') && this.model.get('teamsIds').length) {
            let teamIdList = this.model.get('teamsIds');
            let teamNameHash = this.model.get('teamsNames') || {};
            this.messageName = 'postTargetTeam';

            if (teamIdList.length > 1) {
                this.messageName = 'postTargetTeams';
            }

            let teamHtmlList = [];

            teamIdList.forEach(teamId => {
                let teamName = teamNameHash[teamId];

                if (!teamName) {
                    return;
                }

                teamHtmlList.push(
                    $('<a>')
                        .attr('href', '#Team/view/' + teamId)
                        .text(teamName)
                        .get(0).outerHTML
                );
            });

            this.messageData['html:target'] = teamHtmlList.join(', ');

            this.createMessage();

            return;
        }

        if (this.model.has('portalsIds') && this.model.get('portalsIds').length) {
            let portalIdList = this.model.get('portalsIds');
            let portalNameHash = this.model.get('portalsNames') || {};

            this.messageName = 'postTargetPortal';

            if (portalIdList.length > 1) {
                this.messageName = 'postTargetPortals';
            }

            let portalHtmlList = [];

            portalIdList.forEach(portalId =>{
                let portalName = portalNameHash[portalId];

                if (!portalName) {
                    return;
                }

                portalHtmlList.push(
                    $('<a>')
                        .attr('href', '#Portal/view/' + portalId)
                        .text(portalName)
                        .get(0).outerHTML
                )
            });

            this.messageData['html:target'] = portalHtmlList.join(', ');

            this.createMessage();

            return;
        }

        if (!this.model.has('usersIds') || !this.model.get('usersIds').length) {
            this.createMessage();

            return;
        }

        let userIdList = this.model.get('usersIds');
        let userNameHash = this.model.get('usersNames') || {};

        this.messageName = 'postTarget';

        if (userIdList.length === 1 && userIdList[0] === this.model.get('createdById')) {
            this.messageName = 'postTargetSelf';
            this.createMessage();

            return;
        }

        let userHtmlList = [];

        userIdList.forEach(userId => {
            if (userId === this.getUser().id) {
                this.messageName = 'postTargetYou';

                if (userIdList.length > 1) {
                    if (userId === this.model.get('createdById')) {
                        this.messageName = 'postTargetSelfAndOthers';
                    } else {
                        this.messageName = 'postTargetYouAndOthers';
                    }
                }

                return;
            }

            if (userId === this.model.get('createdById')) {
                this.messageName = 'postTargetSelfAndOthers';

                return;
            }

            let userName = userNameHash[userId];

            if (!userName) {
                return;
            }

            userHtmlList.push(
                $('<a>')
                    .attr('href', '#User/view/' + userId)
                    .attr('data-scope', 'User')
                    .attr('data-id', userId)
                    .text(userName)
                    .get(0).outerHTML
            );
        });

        this.messageData['html:target'] = userHtmlList.join(', ');

        this.createMessage();
    }
}

export default PostNoteStreamView;
