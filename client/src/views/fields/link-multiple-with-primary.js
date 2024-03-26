

import LinkMultipleFieldView from 'views/fields/link-multiple';


class LinkMultipleWithPrimaryFieldView extends LinkMultipleFieldView {

    
    primaryLink

    switchPrimary(id) {
        let $switch = this.$el.find(`[data-id="${id}"][data-action="switchPrimary"]`);

        if (!$switch.hasClass('active')) {
            this.$el.find('button[data-action="switchPrimary"]')
                .removeClass('active')
                .children()
                .addClass('text-muted');

            $switch.addClass('active').children().removeClass('text-muted');

            this.setPrimaryId(id);
        }
    }

    
    getAttributeList() {
        const list = super.getAttributeList();

        list.push(this.primaryIdAttribute);
        list.push(this.primaryNameAttribute);

        return list;
    }

    setup() {
        this.primaryLink = this.options.primaryLink || this.primaryLink ||
            this.model.getFieldParam(this.name, 'primaryLink');

        this.primaryIdAttribute = this.primaryLink + 'Id';
        this.primaryNameAttribute = this.primaryLink + 'Name';

        super.setup();

        this.primaryId = this.model.get(this.primaryIdAttribute);
        this.primaryName = this.model.get(this.primaryNameAttribute);

        this.listenTo(this.model, 'change:' + this.primaryIdAttribute, () => {
            this.primaryId = this.model.get(this.primaryIdAttribute);
            this.primaryName = this.model.get(this.primaryNameAttribute);
        });

        this.events['click [data-action="switchPrimary"]'] = e => {
            let $target = $(e.currentTarget);
            let id = $target.data('id');

            this.switchPrimary(id);
        };
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
                itemList.push(this.getDetailLinkHtml(this.primaryId, this.primaryName));
            }

            if (!this.ids.length) {
                return;
            }

            this.ids.forEach(id => {
                if (id !== this.primaryId) {
                    itemList.push(this.getDetailLinkHtml(id));
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

        let $container = this.$el.find('.link-container');

        let $el = $('<div>')
            .addClass('form-inline clearfix ')
            .addClass('list-group-item link-with-role link-group-item-with-primary')
            .addClass('link-' + id)
            .attr('data-id', id);

        let $name = $('<div>').text(name).append('&nbsp;');

        let $remove = $('<a>')
            .attr('role', 'button')
            .attr('tabindex', '0')
            .attr('data-id', id)
            .attr('data-action', 'clearLink')
            .addClass('pull-right')
            .append(
                $('<span>').addClass('fas fa-times')
            );

        let $left = $('<div>');
        let $right = $('<div>');

        $left.append($name);
        $right.append($remove);

        $el.append($left);
        $el.append($right);

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

        $button.insertBefore($el.children().first().children().first());

        $container.append($el);

        this.managePrimaryButton();

        return $el;
    }

    
    managePrimaryButton() {
        let $primary = this.$el.find('button[data-action="switchPrimary"]');

        if ($primary.length > 1) {
            $primary.removeClass('hidden');
        }
        else {
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

export default LinkMultipleWithPrimaryFieldView;
