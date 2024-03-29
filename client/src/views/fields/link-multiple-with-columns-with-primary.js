

import LinkMultipleWithColumnsFieldView from 'views/fields/link-multiple-with-columns';
import LinkMultipleWithPrimaryFieldView from 'views/fields/link-multiple-with-primary';


class LinkMultipleWithColumnsWithPrimaryFieldView extends LinkMultipleWithColumnsFieldView {

    
    primaryLink

    getAttributeList() {
        let list = super.getAttributeList();

        list.push(this.primaryIdAttribute);
        list.push(this.primaryNameAttribute);

        return list;
    }

    setup() {
        this.primaryLink = this.primaryLink || this.model.getFieldParam(this.name, 'primaryLink');

        this.primaryIdAttribute = this.primaryLink + 'Id';
        this.primaryNameAttribute = this.primaryLink + 'Name';

        super.setup();

        this.events['click [data-action="switchPrimary"]'] = e => {
            let $target = $(e.currentTarget);
            let id = $target.data('id');

            LinkMultipleWithPrimaryFieldView.prototype.switchPrimary.call(this, id);
        };

        this.primaryId = this.model.get(this.primaryIdAttribute);
        this.primaryName = this.model.get(this.primaryNameAttribute);

        this.listenTo(this.model, 'change:' + this.primaryIdAttribute, () => {
            this.primaryId = this.model.get(this.primaryIdAttribute);
            this.primaryName = this.model.get(this.primaryNameAttribute);
        });
    }

    setPrimaryId(id) {
        this.primaryId = id;

        this.primaryName = id ?
            this.nameHash[id] : null;

        this.trigger('change');
    }

    renderLinks() {
        if (this.primaryId) {
            this.addLinkHtml(this.primaryId, this.primaryName);
        }

        this.ids.forEach(id => {
            if (id !== this.primaryId) {
                this.addLinkHtml(id, this.nameHash[id]);
            }
        });
    }

    getValueForDisplay() {
        if (this.isDetailMode() || this.isListMode()) {
            let itemList = [];

            if (this.primaryId) {
                itemList.push(
                    this.getDetailLinkHtml(this.primaryId, this.primaryName)
                );
            }

            if (!this.ids.length) {
                return;
            }

            this.ids.forEach(id =>{
                if (id !== this.primaryId) {
                    itemList.push(
                        this.getDetailLinkHtml(id)
                    );
                }
            });

            return itemList
                .map(item => $('<div>').append(item).get(0).outerHTML)
                .join('');
        }
    }

    deleteLink(id) {
        if (id === this.primaryId) {
            this.setPrimaryId(null);
        }

        super.deleteLink(id);
    }

    deleteLinkHtml(id) {
        super.deleteLinkHtml(id);

        this.managePrimaryButton();
    }

    addLinkHtml(id, name) {
        name = name || id;

        if (this.isSearchMode()) {
            return super.addLinkHtml(id, name);
        }

        if (this.skipRoles) {
            return LinkMultipleWithPrimaryFieldView.prototype.addLinkHtml.call(this, id, name);
        }

        let $el = super.addLinkHtml(id, name);

        let isPrimary = (id === this.primaryId);

        let $star = $('<span>')
            .addClass('fas fa-star fa-sm')
            .addClass(!isPrimary ? 'text-muted' : '')

        let $button = $('<button>')
            .attr('type', 'button')
            .addClass('btn btn-link btn-sm pull-right hidden')
            .attr('title', this.translate('Primary'))
            .attr('data-action', 'switchPrimary')
            .attr('data-id', id)
            .append($star);

        $button.insertAfter($el.children().first().children().first());

        this.managePrimaryButton();

        return $el;
    }

    managePrimaryButton() {
        let $primary = this.$el.find('button[data-action="switchPrimary"]');

        if ($primary.length > 1) {
            $primary.removeClass('hidden');
        } else {
            $primary.addClass('hidden');
        }

        if ($primary.filter('.active').length === 0) {
            let $first = $primary.first();

            if ($first.length) {
                $first.addClass('active').children().removeClass('text-muted');
                this.setPrimaryId($first.data('id'));
            }
        }
    }

    fetch() {
        const data = super.fetch();

        data[this.primaryIdAttribute] = this.primaryId;
        data[this.primaryNameAttribute] = this.primaryName;

        return data;
    }
}


export default LinkMultipleWithColumnsWithPrimaryFieldView;
