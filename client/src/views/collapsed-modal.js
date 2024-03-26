

import View from 'view';

class CollapsedModalView extends View {

    templateContent = `
        <div class="title-container">
            <a role="button" data-action="expand" class="title">{{title}}</a>
        </div>
        <div class="close-container">
            <a role="button" data-action="close"><span class="fas fa-times"></span></a>
        </div>
    `

    events = {
        
        'click [data-action="expand"]': function () {
            this.expand();
        },
        
        'click [data-action="close"]': function () {
            this.close();
        },
    }

    data() {
        let title = this.title;

        if (this.duplicateNumber) {
            title = this.title + ' ' + this.duplicateNumber;
        }

        return {
            title: title,
        };
    }

    setup() {
        this.title = this.options.title || 'no-title';
        this.duplicateNumber = this.options.duplicateNumber || null;
    }

    expand() {
        this.trigger('expand');
    }

    close() {
        this.trigger('close');
    }
}


export default CollapsedModalView;
