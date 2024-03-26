

import Ajax from 'ajax';


class KnowledgeBaseHelper {

    
    constructor(language) {
        this.language = language;
    }

    getAttributesForEmail(model, attributes, callback) {
        attributes = attributes || {};
        attributes.body = model.get('body');

        if (attributes.name) {
            attributes.name = attributes.name + ' ';
        } else {
            attributes.name = '';
        }

        attributes.name += this.language.translate('KnowledgeBaseArticle', 'scopeNames') + ': ' +
            model.get('name');

        Ajax.postRequest('KnowledgeBaseArticle/action/getCopiedAttachments', {
            id: model.id,
            parentType: 'Email',
            field : 'attachments',
        }).then(data => {
            attributes.attachmentsIds = data.ids;
            attributes.attachmentsNames = data.names;
            attributes.isHtml = true;

            callback(attributes);
        });
    }
}

export default KnowledgeBaseHelper;
