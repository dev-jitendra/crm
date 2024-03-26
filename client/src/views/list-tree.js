

import ListView from 'views/list';

class ListTreeView extends ListView {

    searchPanel = false
    createButton = false

    name = 'listTree'

    getRecordViewName() {
        return this.getMetadata().get(['clientDefs', this.scope, 'recordViews', 'listTree']) ||
            'views/record/list-tree';
    }
}

export default ListTreeView
