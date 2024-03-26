

import SelectRecordsModalView from 'views/modals/select-records';

class SelectTemplateModalView extends SelectRecordsModalView {

    multiple = false
    createButton = false
    searchPanel = false
    scope = 'Template'
    backdrop = true

    setupSearch() {
        super.setupSearch();

        this.searchManager.setAdvanced({
            entityType: {
                type: 'equals',
                value: this.options.entityType,
            },
        });

        this.collection.where = this.searchManager.getWhere();
    }

    afterRender() {
        super.afterRender();

        const firstLinkElement = this.$el.find('a.link').first().get(0);

        if (firstLinkElement) {
            
            setTimeout(() => firstLinkElement.focus({preventScroll: true}), 10);
        }
    }
}

export default SelectTemplateModalView;
