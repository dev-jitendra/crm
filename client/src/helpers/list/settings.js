

class ListSettingsHelper {

    
    constructor(entityType, type, userId, storage) {
        
        this.storage = storage;

        this.layoutColumnsKey = `${type}-${entityType}-${userId}`;
        this.hiddenColumnMapCache = {};
    }

    
    getHiddenColumnMap() {
        if (this.hiddenColumnMapCache[this.layoutColumnsKey]) {
            return this.hiddenColumnMapCache[this.layoutColumnsKey];
        }

        return this.storage.get('listHiddenColumns', this.layoutColumnsKey) || {};
    }

    
    storeHiddenColumnMap(map) {
        delete this.hiddenColumnMapCache[this.layoutColumnsKey];

        this.storage.set('listHiddenColumns', this.layoutColumnsKey, map);
    }

    
    clearHiddenColumnMap() {
        delete this.hiddenColumnMapCache[this.layoutColumnsKey];

        this.storage.clear('listHiddenColumns', this.layoutColumnsKey);
    }
}

export default ListSettingsHelper;
