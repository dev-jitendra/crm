

import View from 'view';

class HomeView extends View {

    template = 'home'

    setup() {
        const viewName = this.getMetadata().get(['clientDefs', 'Home', 'view']) ||
            'views/dashboard';

        this.createView('content', viewName, {selector: '> .home-content'});
    }
}

export default HomeView;
