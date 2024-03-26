

import View from 'view';

class AboutView extends View {

    template = 'about'

    data() {
        return {
            version: this.version,
            text: this.getHelper().transformMarkdownText(this.text)
        };
    }

    setup() {
        this.wait(
            Espo.Ajax.getRequest('App/about')
                .then(data => {
                    this.text = data.text;
                    this.version = data.version;
                })
        );
    }
}

export default AboutView;
