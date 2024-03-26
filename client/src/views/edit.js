



import MainView from 'views/main';


class EditView extends MainView {

    
    template = 'edit'

    
    name = 'Edit'

    
    optionsToPass = [
        'returnUrl',
        'returnDispatchParams',
        'attributes',
        'rootUrl',
        'duplicateSourceId',
        'returnAfterCreate',
    ]

    
    headerView = 'views/header'

    
    recordView = 'views/record/edit'

    
    rootLinkDisabled = false

    
    setup() {
        this.headerView = this.options.headerView || this.headerView;
        this.recordView = this.options.recordView || this.recordView;

        this.setupHeader();
        this.setupRecord();
    }

    
    setupFinal() {
        super.setupFinal();

        this.wait(
            this.getHelper().processSetupHandlers(this, 'edit')
        );
    }

    
    setupHeader() {
        this.createView('header', this.headerView, {
            model: this.model,
            fullSelector: '#main > .header',
            scope: this.scope,
        });
    }

    
    setupRecord() {
        const o = {
            model: this.model,
            fullSelector: '#main > .record',
            scope: this.scope,
            shortcutKeysEnabled: true,
        };

        this.optionsToPass.forEach(option => {
            o[option] = this.options[option];
        });

        const params = this.options.params || {};

        if (params.rootUrl) {
            o.rootUrl = params.rootUrl;
        }

        if (params.focusForCreate) {
            o.focusForCreate = true;
        }

        return this.createView('record', this.getRecordViewName(), o);
    }

    
    getRecordViewName() {
        return this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.edit') || this.recordView;
    }

    
    getHeader() {
        const headerIconHtml = this.getHeaderIconHtml();
        const rootUrl = this.options.rootUrl || this.options.params.rootUrl || '#' + this.scope;
        const scopeLabel = this.getLanguage().translate(this.scope, 'scopeNamesPlural');

        let $root = $('<span>').text(scopeLabel);

        if (!this.options.noHeaderLinks && !this.rootLinkDisabled) {
            $root =
                $('<span>')
                    .append(
                        $('<a>')
                            .attr('href', rootUrl)
                            .addClass('action')
                            .attr('data-action', 'navigateToRoot')
                            .text(scopeLabel)
                    );
        }

        if (headerIconHtml) {
            $root.prepend(headerIconHtml);
        }

        if (this.model.isNew()) {
            const $create = $('<span>').text(this.getLanguage().translate('create'));

            return this.buildHeaderHtml([$root, $create]);
        }

        const name = this.model.get('name') || this.model.id;

        let $name = $('<span>').text(name);

        if (!this.options.noHeaderLinks) {
            const url = '#' + this.scope + '/view/' + this.model.id;

            $name =
                $('<a>')
                    .attr('href', url)
                    .addClass('action')
                    .append($name);
        }

        return this.buildHeaderHtml([$root, $name]);
    }

    
    updatePageTitle() {
        if (this.model.isNew()) {
            const title = this.getLanguage().translate('Create') + ' ' +
                this.getLanguage().translate(this.scope, 'scopeNames');

            this.setPageTitle(title);

            return;
        }

        const name = this.model.get('name');

        const title = name ? name : this.getLanguage().translate(this.scope, 'scopeNames');

        this.setPageTitle(title);
    }
}

export default EditView;
