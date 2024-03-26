

define('views/note/detail', ['views/main'], (Dep) => {

    
    return Dep.extend({

        templateContent: `
            <div class="header page-header">{{{header}}}</div>
            <div class="record list-container list-container-panel block-center">{{{record}}}</div>
        `,

        
        isDeleted: false,

        setup: function () {
            this.scope = this.model.entityType;

            this.setupHeader();
            this.setupRecord();

            this.listenToOnce(this.model, 'remove', () => {
                this.clearView('record');
                this.isDeleted = true;
                this.getHeaderView().reRender();
            });
        },

        setupHeader: function () {
            this.createView('header', 'views/header', {
                selector: '> .header',
                scope: this.scope,
                fontSizeFlexible: true,
            });
        },

        setupRecord: function () {
            this.wait(
                this.getCollectionFactory().create(this.scope)
                    .then(collection => {
                        this.collection = collection;
                        this.collection.add(this.model);

                        this.createView('record', 'views/stream/record/list', {
                            selector: '> .record',
                            collection: this.collection,
                            isUserStream: true,
                        });
                    })
            );
        },

        getHeader: function () {
            let parentType = this.model.get('parentType');
            let parentId = this.model.get('parentId');
            let parentName = this.model.get('parentName');
            let type = this.model.get('type');

            let $type = $('<span>')
                    .text(this.getLanguage().translateOption(type, 'type', 'Note'));

            if (this.model.get('deleted') || this.isDeleted) {
                $type.css('text-decoration', 'line-through');
            }

            if (parentType && parentId) {
                return this.buildHeaderHtml([
                    $('<a>')
                        .attr('href', '#' + parentType)
                        .text(this.translate(parentType, 'scopeNamesPlural')),
                    $('<a>')
                        .attr('href', '#' + parentType + '/view/' + parentId)
                        .text(parentName || parentId),
                    $('<span>')
                        .text(this.translate('Stream', 'scopeNames')),
                    $type,
                ]);
            }

            return this.buildHeaderHtml([
                $('<span>')
                    .text(this.translate('Stream', 'scopeNames')),
                $type,
            ]);
        },
    });
});
