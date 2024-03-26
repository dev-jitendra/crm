

import Controller from 'controller';

class EventConfirmationController extends Controller {

    
    actionConfirmEvent(actionData) {
        const viewName = this.getMetadata().get(['clientDefs', 'EventConfirmation', 'confirmationView']) ||
            'crm:views/event-confirmation/confirmation';

        this.entire(viewName, {
            actionData: actionData,
        }, view => {
            view.render();
        });
    }
}


export default EventConfirmationController;
