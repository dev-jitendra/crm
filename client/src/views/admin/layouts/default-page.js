

import View from 'view';

class LayoutDefaultPageView extends View {

    
    templateContent = `
        <div class="margin-bottom">{{translate 'selectLayout' category='messages' scope='Admin'}}</div>
        <div class="button-container">
            <button data-action="createLayout" class="btn btn-link">{{translate 'Create'}}</button>
        </div>
    `
}

export default LayoutDefaultPageView;
