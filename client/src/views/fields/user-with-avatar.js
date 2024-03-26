

import UserFieldView from 'views/fields/user';

class UserWithAvatarFieldView extends UserFieldView {

    listTemplate = 'fields/user-with-avatar/list'
    detailTemplate = 'fields/user-with-avatar/detail'

    data() {
        let o = super.data();

        if (this.mode === this.MODE_DETAIL) {
            o.avatar = this.getAvatarHtml();
            o.isOwn = this.model.get(this.idName) === this.getUser().id;
        }

        return o;
    }

    getAvatarHtml() {
        return this.getHelper().getAvatarHtml(this.model.get(this.idName), 'small', 14, 'avatar-link');
    }
}

export default UserWithAvatarFieldView;
