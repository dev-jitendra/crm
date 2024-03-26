

 define('crm:views/activities/list', ['views/list-related'], function (Dep) {

    return Dep.extend({

        createButton: false,

        unlinkDisabled: true,

        filtersDisabled: true,

        setup: function () {
            this.rowActionsView = 'views/record/row-actions/default';

            Dep.prototype.setup.call(this);

            this.type = this.options.type;
        },

        getHeader: function () {
            let name = this.model.get('name') || this.model.id;

            let recordUrl = '#' + this.scope  + '/view/' + this.model.id;

            let $name =
                $('<a>')
                    .attr('href', recordUrl)
                    .addClass('font-size-flexible title')
                    .text(name);

            if (this.model.get('deleted')) {
                $name.css('text-decoration', 'line-through');
            }

            let headerIconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);
            let scopeLabel = this.getLanguage().translate(this.scope, 'scopeNamesPlural');

            let $root = $('<span>').text(scopeLabel);

            if (!this.rootLinkDisabled) {
                $root = $('<span>')
                    .append(
                        $('<a>')
                            .attr('href', '#' + this.scope)
                            .addClass('action')
                            .attr('data-action', 'navigateToRoot')
                            .text(scopeLabel)
                    );
            }

            if (headerIconHtml) {
                $root.prepend(headerIconHtml);
            }

            let linkLabel = this.type === 'history' ? this.translate('History') : this.translate('Activities');

            let $link = $('<span>').text(linkLabel);

            let $target = $('<span>').text(this.translate(this.foreignScope, 'scopeNamesPlural'));

            return this.buildHeaderHtml([
                $root,
                $name,
                $link,
                $target,
            ]);
        },

        
        updatePageTitle: function () {
            this.setPageTitle(this.translate(this.foreignScope, 'scopeNamesPlural'));
        },
    });
});
