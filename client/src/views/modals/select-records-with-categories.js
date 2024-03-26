

import SelectRecordsModal from 'views/modals/select-records';
import ListWithCategories from 'views/list-with-categories';

class SelectRecordsWithCategoriesModalView extends SelectRecordsModal {

    template = 'modals/select-records-with-categories'

    
    
    categoryField = 'category'
    
    categoryFilterType = 'inCategory'
    categoryScope = ''
    isExpanded = true

    data() {
        return {
            ...super.data(),
            categoriesDisabled: this.categoriesDisabled,
        };
    }

    setup() {
        this.scope = this.entityType = this.options.scope || this.scope;
        this.categoryScope = this.categoryScope || this.scope + 'Category';

        this.categoriesDisabled = this.categoriesDisabled ||
           this.getMetadata().get(['scopes',  this.categoryScope, 'disabled']) ||
           !this.getAcl().checkScope(this.categoryScope);

        super.setup();
    }

    setupList() {
        if (!this.categoriesDisabled) {
            this.setupCategories();
        }

        super.setupList();
    }

    setupCategories() {
        this.getCollectionFactory().create(this.categoryScope, collection => {
            this.treeCollection = collection;

            collection.url = collection.entityType + '/action/listTree';
            collection.data.onlyNotEmpty = true;

            collection.fetch()
                .then(() => this.createCategoriesView());
        });
    }

    createCategoriesView() {
        this.createView('categories', 'views/record/list-tree', {
            collection: this.treeCollection,
            selector: '.categories-container',
            selectable: true,
            readOnly: true,
            showRoot: true,
            rootName: this.translate(this.scope, 'scopeNamesPlural'),
            buttonsDisabled: true,
            checkboxes: false,
            isExpanded: this.isExpanded,
        }, view => {
            if (this.isRendered()) {
                view.render();
            } else {
                this.listenToOnce(this, 'after:render', () => view.render());
            }

            this.listenTo(view, 'select', model => {
                this.currentCategoryId = null;
                this.currentCategoryName = '';

                if (model && model.id) {
                    this.currentCategoryId = model.id;
                    this.currentCategoryName = model.get('name');
                }

                this.applyCategoryToCollection();

                Espo.Ui.notify(' ... ');

                this.collection.fetch()
                    .then(() => Espo.Ui.notify(false));
            });
        });
    }

    applyCategoryToCollection() {
        ListWithCategories.prototype.applyCategoryToCollection.call(this);
    }

    
    isCategoryMultiple() {
        ListWithCategories.prototype.isCategoryMultiple.call(this);
    }
}


export default SelectRecordsWithCategoriesModalView;
