

import Controller from 'controller';

class UnsubscribeController extends Controller {

    
    actionUnsubscribe(data) {
        const viewName = data.view || 'crm:views/campaign/unsubscribe';

        this.entire(viewName, {
            actionData: data.actionData,
            template: data.template,
        }, view => {
            view.render();
        });
    }

    
    actionSubscribeAgain(data) {
        const viewName = data.view || 'crm:views/campaign/subscribe-again';

        this.entire(viewName, {
            actionData: data.actionData,
            template: data.template,
        }, view => {
            view.render();
        });
    }
}

export default UnsubscribeController;
