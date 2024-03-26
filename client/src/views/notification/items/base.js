



import View from 'view';

class BaseNotificationItemView extends View {

    
    messageName
    
    messageTemplate
    messageData = null
    isSystemAvatar = false

    data() {
        return {
            avatar: this.getAvatarHtml(),
        };
    }

    init() {
        this.createField('createdAt', null, null, 'views/fields/datetime-short');

        this.messageData = {};
    }

    createField(name, type, params, view) {
        type = type || this.model.getFieldType(name) || 'base';

        this.createView(name, view || this.getFieldManager().getViewName(type), {
            model: this.model,
            defs: {
                name: name,
                params: params || {}
            },
            selector: '.cell-' + name,
            mode: 'list',
        });
    }

    createMessage() {
        let parentType = this.model.get('relatedParentType') || null;

        if (!this.messageTemplate && this.messageName) {
            this.messageTemplate = this.translate(this.messageName, 'notificationMessages', parentType) || '';
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
        let id = this.userId;

        if (this.isSystemAvatar || !id) {
            id = this.getHelper().getAppParam('systemUserId');
        }

        return this.getHelper().getAvatarHtml(id, 'small', 20);
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
}

export default BaseNotificationItemView;
