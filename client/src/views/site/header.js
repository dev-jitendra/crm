

import View from 'view';

class HeaderSiteView extends View {

    template = 'site/header'

    title = 'EspoCRM'
    navbarView = 'views/site/navbar'
    customViewPath = ['clientDefs', 'App', 'navbarView']

    data = {
        title: this.title,
    }

    setup() {
        let navbarView = this.getMetadata().get(this.customViewPath) || this.navbarView;

        this.createView('navbar', navbarView, {
            fullSelector: '#navbar',
            title: this.title,
        });
    }
}

export default HeaderSiteView;
