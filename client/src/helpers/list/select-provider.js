

class SelectProvider {

    
    constructor(layoutManager, metadata, fieldManager) {
        this.layoutManager = layoutManager;
        this.metadata = metadata;
        this.fieldManager = fieldManager;
    }

    
    get(entityType, layoutName) {
        return new Promise(resolve => {
            this.layoutManager.get(entityType, layoutName || 'list', layout => {
                const list = this.getFromLayout(entityType, layout);

                resolve(list);
            });
        });
    }

    
    getFromLayout(entityType, listLayout) {
        let list = [];

        listLayout.forEach(item => {
            if (!item.name) {
                return;
            }

            const field = item.name;
            const fieldType = this.metadata.get(['entityDefs', entityType, 'fields', field, 'type']);

            if (!fieldType) {
                return;
            }

            list = [
                this.fieldManager.getEntityTypeFieldAttributeList(entityType, field),
                ...list
            ];
        });

        return list;
    }
}

export default SelectProvider;
