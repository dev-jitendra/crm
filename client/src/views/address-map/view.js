

define('views/address-map/view', ['views/main'], function (Dep) {

    return Dep.extend({

        templateContent: `
            <div class="header page-header">{{{header}}}</div>
            <div class="map-container">{{{map}}}</div>
        `,

        setup: function () {
            this.scope = this.model.entityType;

            this.createView('header', 'views/header', {
                model: this.model,
                fullSelector: '#main > .header',
                scope: this.model.entityType,
                fontSizeFlexible: true,
            });
        },

        afterRender: function () {
        	var field = this.options.field;

            var viewName = this.model.getFieldParam(field + 'Map', 'view') ||
                this.getFieldManager().getViewName('map');

            this.createView('map', viewName, {
                model: this.model,
                name: field + 'Map',
                selector: '.map-container',
                height: this.getHelper().calculateContentContainerHeight(this.$el.find('.map-container')),
            }, (view) => {
            	view.render();
            });
        },

        getHeader: function () {
            let name = this.model.get('name');

            if (!name) {
                name = this.model.id;
            }

            let recordUrl = '#' + this.model.entityType + '/view/' + this.model.id
            let scopeLabel = this.getLanguage().translate(this.model.entityType, 'scopeNamesPlural');
            let fieldLabel = this.translate(this.options.field, 'fields', this.model.entityType);
            let rootUrl = this.options.rootUrl ||
                this.options.params.rootUrl ||
                '#' + this.model.entityType;

            let $name = $('<a>')
                .attr('href', recordUrl)
                .append(
                    $('<span>')
                    .addClass('font-size-flexible title')
                    .text(name)
                );

            if (this.model.get('deleted')) {
                $name.css('text-decoration', 'line-through');
            }

            let $root = $('<span>')
                .append(
                    $('<a>')
                        .attr('href', rootUrl)
                        .addClass('action')
                        .attr('data-action', 'navigateToRoot')
                        .text(scopeLabel)
                );

            let headerIconHtml = this.getHeaderIconHtml();

            if (headerIconHtml) {
                $root.prepend(headerIconHtml);
            }

            let $field = $('<span>').text(fieldLabel)

            return this.buildHeaderHtml([
                $root,
                $name,
                $field,
            ]);
        },
    });
});
