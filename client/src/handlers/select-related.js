






class SelectRelatedHandler {

    
    constructor(viewHelper) {
        
        
        this.viewHelper = viewHelper;
    }

    
    getFilters(model) {
        return Promise.resolve({});
    }
}

export default SelectRelatedHandler;
