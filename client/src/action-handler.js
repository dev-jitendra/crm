



import {View as BullView} from 'bullbone';


class ActionHandler {

    
    constructor(view) {
        
        this.view = view;
    }
}

ActionHandler.extend = BullView.extend;

export default ActionHandler;
