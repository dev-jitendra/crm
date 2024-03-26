

import Controller from 'controller';

class NoteController extends Controller {

    
    
    actionView(options) {
        const id = options.id;

        if (!id) {
            throw new Espo.Exceptions.NotFound;
        }

        const viewName = this.getMetadata().get(['clientDefs', this.name, 'views', 'detail']) ||
            'views/note/detail';

        let model;

        this.showLoadingNotification();

        this.modelFactory.create('Note')
            .then(m => {
                model = m;
                model.id = id;

                return model.fetch({main: true});
            })
            .then(() => {
                this.hideLoadingNotification();

                this.main(viewName, {model: model});
            });
    }
}

export default NoteController;
