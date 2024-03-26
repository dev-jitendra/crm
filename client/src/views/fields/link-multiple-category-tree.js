

import LinkMultipleFieldView from 'views/fields/link-multiple';

class LinkMultipleCategoryTreeFieldView extends LinkMultipleFieldView {

    selectRecordsView = 'views/modals/select-category-tree-records'
    autocompleteDisabled = false

    getUrl(id) {
        return '#' + this.entityType + '/list/categoryId=' + id;
    }

    fetchSearch() {
        const data = super.fetchSearch();

        if (!data) {
            return data;
        }

        data.type = 'inCategory';

        return data;
    }
}


export default LinkMultipleCategoryTreeFieldView;

