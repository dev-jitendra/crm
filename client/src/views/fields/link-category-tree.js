

import LinkFieldView from 'views/fields/link';

class LinkCategoryTreeFieldView extends LinkFieldView {

    selectRecordsView = 'views/modals/select-category-tree-records'
    autocompleteDisabled = false

    fetchSearch() {
        const data = super.fetchSearch();

        if (!data) {
            return data;
        }

        if (data.typeFront === 'is') {
            data.field = this.name;
            data.type = 'inCategory';
        }

        return data;
    }

    getUrl() {
        const id = this.model.get(this.idName);

        if (!id) {
            return null;
        }

        return '#' + this.entityType + '/list/categoryId=' + id;
    }
}


export default LinkCategoryTreeFieldView;
