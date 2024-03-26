

import RowActionHandler from 'handlers/row-action';

class MoveActionHandler extends RowActionHandler {

    isAvailable(model, action) {
        return model.collection &&
            model.collection.orderBy === 'order' &&
            model.collection.order === 'asc';
    }

    process(model, action) {
        if (action === 'moveToTop') {
            this.moveToTop(model);

            return;
        }

        if (action === 'moveToBottom') {
            this.moveToBottom(model);

            return;
        }

        if (action === 'moveUp') {
            this.moveUp(model);

            return;
        }

        if (action === 'moveDown') {
            this.moveDown(model);
        }
    }

    moveToTop(model) {
        const index = this.collection.indexOf(model);

        if (index === 0) {
            return;
        }

        Espo.Ui.notify(' ... ');

        Espo.Ajax.postRequest('KnowledgeBaseArticle/action/moveToTop', {
            id: model.id,
            where: this.collection.getWhere(),
        }).then(() => {
            this.collection.fetch()
                .then(() => Espo.Ui.notify(false));
        });
    }

    moveUp(model) {
        const index = this.collection.indexOf(model);

        if (index === 0) {
            return;
        }

        Espo.Ui.notify(' ... ');

        Espo.Ajax.postRequest('KnowledgeBaseArticle/action/moveUp', {
            id: model.id,
            where: this.collection.getWhere(),
        }).then(() => {
            this.collection.fetch()
                .then(() => Espo.Ui.notify(false));
        });
    }

    moveDown(model) {
        const index = this.collection.indexOf(model);

        if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) {
            return;
        }

        Espo.Ui.notify(' ... ');

        Espo.Ajax.postRequest('KnowledgeBaseArticle/action/moveDown', {
            id: model.id,
            where: this.collection.getWhere(),
        }).then(() => {
            this.collection.fetch()
                .then(() => Espo.Ui.notify(false));
        });
    }

    moveToBottom(model) {
        const index = this.collection.indexOf(model);

        if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) {
            return;
        }

        Espo.Ui.notify(' ... ');

        Espo.Ajax.postRequest('KnowledgeBaseArticle/action/moveToBottom', {
            id: model.id,
            where: this.collection.getWhere(),
        }).then(() => {
            this.collection.fetch()
                .then(() => Espo.Ui.notify(false));
        });
    }
}

export default MoveActionHandler;
