


class RecordModalHelper {
    
    constructor(metadata, acl) {
        this.metadata = metadata;
        this.acl = acl;
    }

    
    showDetail(view, params) {
        const id = params.id;
        const scope = params.scope;
        const model = params.model;

        if (!id || !scope) {
            console.error("Bad data.");

            return Promise.reject();
        }

        if (model && !this.acl.checkScope(model.entityType, 'read')) {
            return Promise.reject();
        }

        const viewName = this.metadata.get(['clientDefs', scope, 'modalViews', 'detail']) ||
            'views/modals/detail';

        Espo.Ui.notify(' ... ');

        const options = {
            scope: scope,
            model: model,
            id: id,
            quickEditDisabled: params.editDisabled,
            rootUrl: params.rootUrl,
        };

        return view.createView('modal', viewName, options, modalView => {
            modalView.render()
                .then(() => Espo.Ui.notify(false));

            view.listenToOnce(modalView, 'remove', () => {
                view.clearView('modal');
            });
        });
    }
}

export default RecordModalHelper;
