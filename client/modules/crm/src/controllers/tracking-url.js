

import Controller from 'controller';

class TrackingUrlController extends Controller {

    
    actionDisplayMessage(data) {
        const viewName = data.view || 'crm:views/campaign/tracking-url';

        this.entire(viewName, {
            message: data.message,
            template: data.template,
        }, view => {
            view.render();
        });
    }
}

export default TrackingUrlController
