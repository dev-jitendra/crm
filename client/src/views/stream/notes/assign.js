

import NoteStreamView from 'views/stream/note';

class AssignNoteStreamView extends NoteStreamView {

    template = 'stream/notes/assign'
    messageName = 'assign'

    init() {
        if (this.getUser().isAdmin()) {
            this.isRemovable = true;
        }

        super.init();
    }

    setup() {
        let data = this.model.get('data');

        this.assignedUserId = data.assignedUserId || null;
        this.assignedUserName = data.assignedUserName || null;

        this.messageData['assignee'] =
            $('<a>')
                .attr('href', '#User/view/' + data.assignedUserId)
                .text(data.assignedUserName);

        if (this.isUserStream) {
            if (this.assignedUserId) {
                if (this.assignedUserId === this.model.get('createdById')) {
                    this.messageName += 'Self';
                } else {
                    if (this.assignedUserId === this.getUser().id) {
                        this.messageName += 'You';
                    }
                }
            } else {
                this.messageName += 'Void';
            }
        } else {
            if (this.assignedUserId) {
                if (this.assignedUserId === this.model.get('createdById')) {
                    this.messageName += 'Self';
                }
            } else {
                this.messageName += 'Void';
            }
        }

        this.createMessage();
    }
}

export default AssignNoteStreamView;
