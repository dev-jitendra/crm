

import RecordController from 'controllers/record';

class LayoutSetController extends RecordController {

    
    
    actionEditLayouts(options) {
        const id = options.id;

        if (!id) {
            throw new Error("ID not passed.");
        }

        this.main('views/layout-set/layouts', {
            layoutSetId: id,
            scope: options.scope,
            type: options.type,
        });
    }
}

export default LayoutSetController;
