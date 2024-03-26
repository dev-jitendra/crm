

import Controller from 'controller';

class LeadCaptureOptInConfirmationController extends Controller {

    
    actionOptInConfirmationSuccess(data) {
        const viewName = this.getMetadata().get(['clientDefs', 'LeadCapture', 'optInConfirmationSuccessView']) ||
            'views/lead-capture/opt-in-confirmation-success';

        this.entire(viewName, {
            resultData: data,
        }, view => {
            view.render();
        });
    }

    
    actionOptInConfirmationExpired(data) {
        const viewName = this.getMetadata().get(['clientDefs', 'LeadCapture', 'optInConfirmationExpiredView']) ||
            'views/lead-capture/opt-in-confirmation-expired';

        this.entire(viewName, {
            resultData: data,
        }, view => {
            view.render();
        });
    }
}


export default LeadCaptureOptInConfirmationController;
