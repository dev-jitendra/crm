

import Controller from 'controller';

class DashboardController extends Controller {

    defaultAction = 'index'

    
    actionIndex() {
        this.main('views/dashboard', {
            displayTitle: true,
        }, view => {
            view.render();
        });
    }
}

export default DashboardController;
