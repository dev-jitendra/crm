

import Controller from 'controller';

class NotificationController extends Controller {

    defaultAction = 'index'

    
    actionIndex() {
        this.main('views/notification/list', {}, view => {
            view.render();
        });
    }
}

export default NotificationController;
