

import {Events} from 'bullbone';


class WorkingTimeRangeHandler {

    constructor(view) {
        
        this.view = view;
    }

    process() {
        this.listenTo(this.view.model, 'change:dateStart', (model, value, o) => {
            if (!o.ui || model.get('dateEnd')) {
                return;
            }

            setTimeout(() => model.set('dateEnd', value), 50);
        });
    }
}

Object.assign(WorkingTimeRangeHandler.prototype, Events);

export default WorkingTimeRangeHandler;
