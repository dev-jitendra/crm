


class CreateRelatedHandler {

    
    constructor(viewHelper) {
        
        this.viewHelper = viewHelper;
    }

    
    getAttributes(model) {
        return Promise.resolve({});
    }
}

export default CreateRelatedHandler;
