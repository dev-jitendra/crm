

import Controller from 'controller';

class AboutController extends Controller {

    defaultAction = 'about'

    
    actionAbout() {
        this.main('About', {}, view => {
            view.render();
        });
    }
}

export default AboutController;
