



import View from 'view';

class ListNestedCategoriesRecordView extends View {

    template = 'record/list-nested-categories'

    isLoading = false

    events = {
        'click .action': function (e) {
            Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget);
        },
    }

    data() {
        const data = {};

        if (!this.isLoading) {
            data.list = this.getDataList();
        }

        data.scope = this.collection.entityType;
        data.isLoading = this.isLoading;
        data.currentId = this.collection.currentCategoryId;
        data.currentName = this.collection.currentCategoryName;
        data.categoryData = this.collection.categoryData;

        data.hasExpandedToggler = this.options.hasExpandedToggler;
        data.showEditLink = this.options.showEditLink;
        data.hasNavigationPanel = this.options.hasNavigationPanel;

        const categoryData = this.collection.categoryData || {};

        data.upperLink = categoryData.upperId ?
            '#' + this.subjectEntityType + '/list/categoryId=' + categoryData.upperId:
            '#' + this.subjectEntityType;

        return data;
    }

    getDataList() {
        const list = [];

        this.collection.forEach(model => {
            const o = {
                id: model.id,
                name: model.get('name'),
                recordCount: model.get('recordCount'),
                isEmpty: model.get('isEmpty'),
                link: '#' + this.subjectEntityType + '/list/categoryId=' + model.id,
            };

            list.push(o);
        });

        return list;
    }

    setup() {
        this.listenTo(this.collection, 'sync', () => {
            this.reRender();
        });

        this.subjectEntityType = this.options.subjectEntityType;
    }

    
    actionShowMore() {
        this.$el.find('.category-item.show-more').addClass('hidden');

        this.collection.fetch({
            remove: false,
            more: true,
        });
    }
}


export default ListNestedCategoriesRecordView;
