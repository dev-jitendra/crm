



import {View as BullView} from 'bullbone';


class DynamicHandler {

    
    constructor(recordView) {

        
        this.recordView = recordView;

        
        this.model = recordView.model;
    }

    
    init() {}

    
    onChange(model, o) {}

    
    getMetadata() {
        return this.recordView.getMetadata()
    }
}

DynamicHandler.extend = BullView.extend;


export default DynamicHandler;
