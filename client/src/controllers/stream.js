

import Controller from 'controller';

class StreamController extends Controller {

    defaultAction = 'index'

    
    actionIndex() {
        this.main('views/stream', {
            displayTitle: true,
        }, view => {
            view.render();
        });
    }

    
    actionPosts() {
        this.main('views/stream', {
            displayTitle: true,
            filter: 'posts',
        }, view => {
            view.render();
        });
    }

    
    actionUpdates() {
        this.main('views/stream', {
            displayTitle: true,
            filter: 'updates',
        }, view => {
            view.render();
        });
    }
}

export default StreamController;
