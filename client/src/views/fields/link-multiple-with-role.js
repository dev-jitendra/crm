

import LinkMultipleFieldView from 'views/fields/link-multiple';
import Select from 'ui/select';


class LinkMultipleWithRoleFieldView extends LinkMultipleFieldView {

    
    roleType = 'enum'
    
    columnName = 'role'
    
    roleFieldIsForeign = true
    
    emptyRoleValue = null
    
    rolePlaceholderText = null
    
    roleMaxLength = 50

    
    ROLE_TYPE_ENUM = 'enum'
    
    
    ROLE_TYPE_VARCHAR = 'varchar'

    setup() {
        super.setup();

        this.columnsName = this.name + 'Columns';
        this.columns = Espo.Utils.cloneDeep(this.model.get(this.columnsName) || {});

        this.listenTo(this.model, 'change:' + this.columnsName, () => {
            this.columns = Espo.Utils.cloneDeep(this.model.get(this.columnsName) || {});
        });

        this.roleField = this.getMetadata()
            .get(['entityDefs', this.model.entityType, 'fields', this.name, 'columns', this.columnName]);

        this.displayRoleAsLabel = this.getMetadata()
            .get(['entityDefs', this.model.entityType, 'fields', this.roleField, 'displayAsLabel']);

        this.roleFieldScope = this.roleFieldIsForeign ? this.foreignScope : this.model.entityType;

        if (this.roleType === this.ROLE_TYPE_ENUM && !this.forceRoles) {
            this.roleList = this.getMetadata()
                .get(['entityDefs', this.roleFieldScope, 'fields', this.roleField, 'options']);

            if (!this.roleList) {
                this.roleList = [];
                this.skipRoles = true;
            }
        }
    }

    getAttributeList() {
        const list = super.getAttributeList();

        list.push(this.name + 'Columns');

        return list;
    }

    getDetailLinkHtml(id, name) {
        

        name = name || this.nameHash[id] || id;

        if (!name && id) {
            name = this.translate(this.foreignScope, 'scopeNames');
        }

        let role = (this.columns[id] || {})[this.columnName] || '';

        if (this.emptyRoleValue && role === this.emptyRoleValue) {
            role = '';
        }

        let $el = $('<div>')
            .append(
                $('<a>')
                    .attr('href', '#' + this.foreignScope + '/view/' + id)
                    .attr('data-id', id)
                    .text(name)
            );

        if (this.isDetailMode()) {
            let iconHtml = this.getIconHtml(id);

            if (iconHtml) {
                $el.prepend(iconHtml);
            }
        }

        if (role) {
            let style = this.getMetadata()
                .get(['entityDefs', this.model.entityType, 'fields', this.roleField, 'style', role]);

            let className = 'text';

            if (this.displayRoleAsLabel && style && style !== 'default') {
                className = 'label label-sm label';

                if (style === 'muted') {
                    style = 'default';
                }
            } else {
                style = style || 'muted';
            }

            className = className + '-' + style;

            let text = this.roleType === this.ROLE_TYPE_ENUM ?
                this.getLanguage().translateOption(role, this.roleField, this.roleFieldScope) :
                role;

            $el.append(
                $('<span>').text(' '),
                $('<span>').addClass('text-muted chevron-right'),
                $('<span>').text(' '),
                $('<span>').text(text).addClass('small').addClass(className)
            );
        }

        return $el.get(0).outerHTML;
    }

    getValueForDisplay() {
        if (this.isDetailMode() || this.isListMode()) {
            let names = [];

            this.ids.forEach(id => {
                names.push(
                    this.getDetailLinkHtml(id)
                );
            });

            return names.join('');
        }
    }

    deleteLink(id) {
        this.trigger('delete-link', id);
        this.trigger('delete-link:' + id);

        this.deleteLinkHtml(id);

        let index = this.ids.indexOf(id);

        if (index > -1) {
            this.ids.splice(index, 1);
        }

        delete this.nameHash[id];
        delete this.columns[id];

        this.afterDeleteLink(id);
        this.trigger('change');
    }

    addLink(id, name) {
        if (!~this.ids.indexOf(id)) {
            this.ids.push(id);
            this.nameHash[id] = name;
            this.columns[id] = {};
            this.columns[id][this.columnName] = null;
            this.addLinkHtml(id, name);

            this.trigger('add-link', id);
            this.trigger('add-link:' + id);
        }

        this.trigger('change');
    }


    
    getJQSelect(id, roleValue) {
        

        let $role = $('<select>')
            .addClass('role form-control input-sm')
            .attr('data-id', id);

        this.roleList.forEach(role => {
            let text = this.getLanguage().translateOption(role, this.roleField, this.roleFieldScope);

            let $option = $('<option>')
                .val(role)
                .text(text);

            if (role === (roleValue || '')) {
                $option.attr('selected', 'selected');
            }

            $role.append($option);
        });

        return $role;
    }

    
    addLinkHtml(id, name) {
        

        name = name || id;

        if (this.isSearchMode() || this.skipRoles) {
            return super.addLinkHtml(id, name);
        }

        let role = (this.columns[id] || {})[this.columnName];

        let $container = this.$el.find('.link-container');

        let $el = $('<div>')
            .addClass('form-inline clearfix')
            .addClass('list-group-item link-with-role link-group-item-with-columns')
            .addClass('link-' + id);

        let $remove = $('<a>')
            .attr('role', 'button')
            .attr('tabindex', '0')
            .attr('data-id', id)
            .attr('data-action', 'clearLink')
            .addClass('pull-right')
            .append(
                $('<span>').addClass('fas fa-times')
            );

        let $left = $('<div>').addClass('pull-left');
        let $right = $('<div>').append($remove);

        let $name = $('<div>')
            .addClass('link-item-name')
            .text(name)
            .append('&nbsp;')

        let $role;

        if (this.roleType === this.ROLE_TYPE_ENUM) {
            $role = this.getJQSelect(id, role);
        }
        else {
            let text = this.rolePlaceholderText || this.translate(this.roleField, 'fields', this.roleFieldScope);

            $role = $('<input>')
                .addClass('role form-control input-sm')
                .attr('maxlength', this.roleMaxLength) 
                .attr('placeholder', text)
                .attr('data-id', id)
                .attr('value', role || '');
        }

        if ($role) {
            $left.append($('<span>')
                .addClass('link-item-column')
                .addClass('link-item-column-' + $role.get(0).tagName.toLowerCase())
                .append($role)
            );
        }

        $left.append($name);
        $el.append($left).append($right);
        $container.append($el);

        if ($role && $role.get(0).tagName === 'SELECT') {
            Select.init($role);
        }

        if (this.isEditMode() && $role) {
            let fetch = ($target) => {
                if (!$target || !$target.length) {
                    return;
                }

                if ($target.val() === null) {
                    return;
                }

                let value = $target.val().toString().trim();
                let id = $target.data('id');

                if (value === '') {
                    value = null;
                }

                this.columns[id] = this.columns[id] || {};
                this.columns[id][this.columnName] = value;
            };

            $role.on('change', e => {
                fetch($(e.currentTarget));
                this.trigger('change');
            });

            fetch($role);
        }

        return $el;
    }

    fetch() {
        let data = super.fetch();

        if (!this.skipRoles) {
            data[this.columnsName] = Espo.Utils.cloneDeep(this.columns);
        }

        return data;
    }
}


export default LinkMultipleWithRoleFieldView;
