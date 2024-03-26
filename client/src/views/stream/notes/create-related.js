

import NoteStreamView from 'views/stream/note';

class CreateRelatedNoteStreamView extends NoteStreamView {

    template = 'stream/notes/create-related'
    messageName = 'createRelated'

    data() {
        return {
            ...super.data(),
            relatedTypeString: this.translateEntityType(this.entityType),
            iconHtml: this.getIconHtml(this.entityType, this.entityId),
        };
    }

    init() {
        if (this.getUser().isAdmin()) {
            this.isRemovable = true;
        }

        super.init();
    }

    setup() {
        let data = this.model.get('data') || {};

        this.entityType = this.model.get('relatedType') || data.entityType || null;
        this.entityId = this.model.get('relatedId') || data.entityId || null;
        this.entityName = this.model.get('relatedName') ||  data.entityName || null;

        this.messageData['relatedEntityType'] = this.translateEntityType(this.entityType);

        this.messageData['relatedEntity'] =
            $('<a>')
                .attr('href', '#' + this.entityType + '/view/' + this.entityId)
                .text(this.entityName);

        this.createMessage();
    }
}

export default CreateRelatedNoteStreamView;
