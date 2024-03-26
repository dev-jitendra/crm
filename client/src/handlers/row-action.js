




class RowActionHandler {

    
    constructor(view) {
        
        
        this.view = view;

        
        this.collection = this.view.collection;
    }

    isAvailable(model, action) {
        return true;
    }

    
    process(model, action) {}
}

export default RowActionHandler;
