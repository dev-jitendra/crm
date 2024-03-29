

import View from 'view';

class NoteStreamView extends View {

    
    messageName = null

    
    messageTemplate = null

    
    messageData = null

    
    isEditable = false

    
    isRemovable = false

    
    isSystemAvatar = false

    data() {
        return {
            isUserStream: this.isUserStream,
            noEdit: this.options.noEdit,
            acl: this.options.acl,
            onlyContent: this.options.onlyContent,
            avatar: this.getAvatarHtml(),
        };
    }

    init() {
        this.createField('createdAt', null, null, 'views/fields/datetime-short');

        this.isUserStream = this.options.isUserStream;
        this.isThis = !this.isUserStream;

        this.parentModel = this.options.parentModel;

        if (!this.isUserStream) {
            if (this.parentModel) {
                if (
                    this.parentModel.entityType !== this.model.get('parentType') ||
                    this.parentModel.id !== this.model.get('parentId')
                ) {
                    this.isThis = false;
                }
            }
        }

        if (this.getUser().isAdmin()) {
            this.isRemovable = true;
        }

        if (this.messageName && this.isThis) {
            this.messageName += 'This';
        }

        if (!this.isThis) {
            this.createField('parent');
        }

        let translatedEntityType = this.translateEntityType(this.model.get('parentType'));

        this.messageData = {
            'user': 'field:createdBy',
            'entity': 'field:parent',
            'entityType': translatedEntityType,
        };

        if (!this.options.noEdit && (this.isEditable || this.isRemovable)) {
            this.createView('right', 'views/stream/row-actions/default', {
                selector: '.right-container',
                acl: this.options.acl,
                model: this.model,
                isEditable: this.isEditable,
                isRemovable: this.isRemovable,
            });
        }
    }

    translateEntityType(entityType, isPlural) {
        let string = isPlural ?
            (this.translate(entityType, 'scopeNamesPlural') || '') :
            (this.translate(entityType, 'scopeNames') || '');

        string = string.toLowerCase();

        let language = this.getPreferences().get('language') || this.getConfig().get('language');

        if (~['de_DE', 'nl_NL'].indexOf(language)) {
            string = Espo.Utils.upperCaseFirst(string);
        }

        return string;
    }

    createField(name, type, params, view, options) {
        type = type || this.model.getFieldType(name) || 'base';

        let o = {
            model: this.model,
            defs: {
                name: name,
                params: params || {}
            },
            selector: '.cell-' + name,
            mode: 'list',
        };

        if (options) {
            for (let i in options) {
                o[i] = options[i];
            }
        }

        this.createView(name, view || this.getFieldManager().getViewName(type), o);
    }

    isMale() {
        return this.model.get('createdByGender') === 'Male';
    }

    isFemale() {
        return this.model.get('createdByGender') === 'Female';
    }

    createMessage() {
        if (!this.messageTemplate) {
            let isTranslated = false;
            let parentType = this.model.get('parentType') || null;

            if (this.isMale()) {
                this.messageTemplate = this.translate(this.messageName, 'streamMessagesMale', parentType) || '';

                if (this.messageTemplate !== this.messageName) {
                    isTranslated = true;
                }
            } else if (this.isFemale()) {
                this.messageTemplate = this.translate(this.messageName, 'streamMessagesFemale', parentType) || '';

                if (this.messageTemplate !== this.messageName) {
                    isTranslated = true;
                }
            }

            if (!isTranslated) {
                this.messageTemplate = this.translate(this.messageName, 'streamMessages', parentType) || '';
            }
        }

        if (
            this.messageTemplate.indexOf('{entityType}') === 0 &&
            typeof this.messageData.entityType === 'string'
        ) {
            this.messageData.entityTypeUcFirst = Espo.Utils.upperCaseFirst(this.messageData.entityType);

            this.messageTemplate = this.messageTemplate.replace('{entityType}', '{entityTypeUcFirst}');
        }

        this.createView('message', 'views/stream/message', {
            messageTemplate: this.messageTemplate,
            selector: '.message',
            model: this.model,
            messageData: this.messageData,
        });
    }

    getAvatarHtml() {
        let id = this.model.get('createdById');

        if (this.isSystemAvatar) {
            id = this.getHelper().getAppParam('systemUserId');
        }

        return this.getHelper().getAvatarHtml(id, 'small', 20);
    }

    getIconHtml(scope, id) {
        if (this.isThis && scope === this.parentModel.entityType) {
            return;
        }

        let iconClass = this.getMetadata().get(['clientDefs', scope, 'iconClass']);

        if (!iconClass) {
            return;
        }

        return $('<span>')
            .addClass(iconClass)
            .addClass('action text-muted icon')
            .css('cursor', 'pointer')
            .attr('title', this.translate('View'))
            .attr('data-action', 'quickView')
            .attr('data-id', id)
            .attr('data-scope', scope)
            .get(0).outerHTML;
    }
}

export default NoteStreamView;
