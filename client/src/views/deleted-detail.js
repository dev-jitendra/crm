

import DetailView from 'views/detail';

class DeletedDetailView extends DetailView {

    recordView = 'views/record/deleted-detail'

    menuDisabled = true

    setup() {
        super.setup();

        if (this.model.get('deleted')) {
            this.menuDisabled = true;
        }
    }

    getRecordViewName() {
        return this.recordView;
    }
}


export default DeletedDetailView;
