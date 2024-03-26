

import ListView from 'views/list';

class ImportListView extends ListView {

    createButton = false

    setup() {
        super.setup();

        this.menu.buttons.unshift({
            iconHtml: '<span class="fas fa-plus fa-sm"></span>',
            text: this.translate('New Import', 'labels', 'Import'),
            link: '#Import',
            acl: 'edit',
        });
    }
}

export default ImportListView;
