

import BaseFieldView from 'views/fields/base';

export default class extends BaseFieldView {

    detailTemplateContent = `
        <a href="#User/view/{{id}}">{{name}}</a>
    `

    data() {
        return {
            id: this.model.get('$user.id'),
            name: this.model.get('name'),
        }
    }
}
