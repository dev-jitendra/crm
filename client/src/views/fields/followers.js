

import LinkMultipleFieldView from 'views/fields/link-multiple';

class FollowersFieldView extends LinkMultipleFieldView {

    foreignScope = 'User'
    portionSize = 6

    setup() {
        super.setup();

        this.addActionHandler('showMoreFollowers', (e, target) => {
            this.showMoreFollowers();

            $(target).remove();
        });

        this.portionSize = this.getConfig().get('recordFollowersLoadLimit') || this.portionSize;

        this.limit = this.portionSize;

        this.listenTo(this.model, 'change:isFollowed', () => {
            let idList = this.model.get(this.idsName) || [];

            if (this.model.get('isFollowed')) {
                if (!~idList.indexOf(this.getUser().id)) {
                    idList.unshift(this.getUser().id);

                    let nameMap = this.model.get(this.nameHashName) || {};

                    nameMap[this.getUser().id] = this.getUser().get('name');

                    this.model.trigger('change:' + this.idsName);

                    this.reRender();
                }

                return;
            }

            let index = idList.indexOf(this.getUser().id);

            if (~index) {
                idList.splice(index, 1);

                this.model.trigger('change:' + this.idsName);

                this.reRender();
            }
        });
    }

    

    showMoreFollowers() {
        this.getCollectionFactory().create('User', collection => {
            collection.url = this.model.entityType + '/' + this.model.id + '/followers';
            collection.offset = this.ids.length || 0;
            collection.maxSize = this.portionSize;
            collection.data.select = ['id', 'name'].join(',');
            collection.orderBy = null;
            collection.order = null;

            this.listenToOnce(collection, 'sync', () => {
                let idList = this.model.get(this.idsName) || [];
                let nameMap = this.model.get(this.nameHashName) || {};

                collection.forEach(user => {
                    idList.push(user.id);
                    nameMap[user.id] = user.get('name');
                });

                this.limit += this.portionSize;

                this.model.trigger('change:' + this.idsName);

                this.reRender();
            });

            collection.fetch();
        });
    }

    getValueForDisplay() {
        if (this.mode === this.MODE_DETAIL || this.mode === this.MODE_LIST) {
            let list = [];

            this.ids.forEach(id => {
                list.push(this.getDetailLinkHtml(id));
            });

            let str = null;

            if (list.length) {
                str = '' + list.join(', ') + '';
            }

            if (list.length >= this.limit) {
                str += ', <a role="button" data-action="showMoreFollowers">...</a>';
            }

            return str;
        }
    }
}

export default FollowersFieldView;
