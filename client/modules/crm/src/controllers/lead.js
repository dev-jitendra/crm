

import RecordController from 'controllers/record';

class LeadController extends RecordController {

    
    actionConvert(id) {
        this.main('crm:views/lead/convert', {id: id});
    }
}

export default LeadController;
