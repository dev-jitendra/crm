



import View from 'view';

class FilterView extends View {

    template = 'search/filter'

    data() {
        return {
            name: this.name,
            scope: this.model.entityType,
            notRemovable: this.options.notRemovable,
        };
    }

    setup() {
        const name = this.name = this.options.name;
        const type = this.model.getFieldType(name);

        if (type) {
            const viewName =
                this.model.getFieldParam(name, 'view') ||
                this.getFieldManager().getViewName(type);

            this.createView('field', viewName, {
                mode: 'search',
                model: this.model,
                selector: '.field',
                defs: {
                    name: name,
                },
                searchParams: this.options.params,
            }, view => {
                this.listenTo(view, 'change', () => {
                    this.trigger('change');
                });

                this.listenTo(view, 'search', () => {
                    this.trigger('search');
                });
            });
        }
    }

    
    getFieldView() {
        return this.getView('field');
    }

    populateDefaults() {
        const view = this.getView('field');

        if (!view) {
            return;
        }

        if (!('populateSearchDefaults' in view)) {
            return;
        }

        view.populateSearchDefaults();
    }
}

export default FilterView;
