

import LinkFieldView from 'views/fields/link';

class UserFieldView extends LinkFieldView {

    searchTemplate = 'fields/user/search'

    setupSearch() {
        super.setupSearch();

        this.searchTypeList = Espo.Utils.clone(this.searchTypeList);
        this.searchTypeList.push('isFromTeams');

        this.searchData.teamIdList = this.getSearchParamsData().teamIdList ||
            this.searchParams.teamIdList || [];
        this.searchData.teamNameHash = this.getSearchParamsData().teamNameHash ||
            this.searchParams.teamNameHash || {};

        this.events['click a[data-action="clearLinkTeams"]'] = e => {
            let id = $(e.currentTarget).data('id').toString();

            this.deleteLinkTeams(id);
        };

        this.addActionHandler('selectLinkTeams', () => {
            Espo.Ui.notify(' ... ');

            let viewName = this.getMetadata().get('clientDefs.Team.modalViews.select') ||
                'views/modals/select-records';

            this.createView('dialog', viewName, {
                scope: 'Team',
                createButton: false,
                multiple: true,
            }, view => {
                view.render();

                Espo.Ui.notify(false);

                this.listenToOnce(view, 'select', models => {
                    if (Object.prototype.toString.call(models) !== '[object Array]') {
                        models = [models];
                    }

                    models.forEach(model => {
                        this.addLinkTeams(model.id, model.get('name'));
                    });
                });
            });
        });

        this.events['click a[data-action="clearLinkTeams"]'] = e => {
            let id = $(e.currentTarget).data('id').toString();

            this.deleteLinkTeams(id);
        };
    }

    handleSearchType(type) {
        super.handleSearchType(type);

        if (type === 'isFromTeams') {
            this.$el.find('div.teams-container').removeClass('hidden');
        }
        else {
            this.$el.find('div.teams-container').addClass('hidden');
        }
    }

    afterRender() {
        super.afterRender();

        if (this.mode === this.MODE_SEARCH) {
            let $elementTeams = this.$el.find('input.element-teams');


            $elementTeams.autocomplete({
                beforeRender: $c => {
                    if (this.$elementName.hasClass('input-sm')) {
                        $c.addClass('small');
                    }
                },
                serviceUrl: () => {
                    return 'Team?&maxSize=' + this.getAutocompleteMaxCount() + '&select=id,name';
                },
                minChars: 1,
                triggerSelectOnValidInput: false,
                paramName: 'q',
                noCache: true,
                formatResult: suggestion => {
                    
                    return this.getHelper().escapeString(suggestion.name);
                },
                transformResult: response => {
                    response = JSON.parse(response);
                    let list = [];

                    response.list.forEach(item => {
                        list.push({
                            id: item.id,
                            name: item.name,
                            data: item.id,
                            value: item.name,
                        });
                    });

                    return {suggestions: list};
                },
                onSelect: s => {
                    this.addLinkTeams(s.id, s.name);

                    $elementTeams.val('');
                    $elementTeams.focus();
                },
            });

            $elementTeams.attr('autocomplete', 'espo-' + this.name);

            this.once('render', () => {
                $elementTeams.autocomplete('dispose');
            });

            this.once('remove', () => {
                $elementTeams.autocomplete('dispose');
            });

            let type = this.$el.find('select.search-type').val();

            if (type === 'isFromTeams') {
                this.searchData.teamIdList.forEach(id => {
                    this.addLinkTeamsHtml(id, this.searchData.teamNameHash[id]);
                });
            }
        }
    }

    deleteLinkTeams(id) {
        this.deleteLinkTeamsHtml(id);

        let index = this.searchData.teamIdList.indexOf(id);

        if (index > -1) {
            this.searchData.teamIdList.splice(index, 1);
        }

        delete this.searchData.teamNameHash[id];

        this.trigger('change');
    }

    addLinkTeams(id, name) {
        this.searchData.teamIdList = this.searchData.teamIdList || [];

        if (!~this.searchData.teamIdList.indexOf(id)) {
            this.searchData.teamIdList.push(id);
            this.searchData.teamNameHash[id] = name;
            this.addLinkTeamsHtml(id, name);

            this.trigger('change');
        }
    }

    deleteLinkTeamsHtml(id) {
        this.$el.find('.link-teams-container .link-' + id).remove();
    }

    addLinkTeamsHtml(id, name) {
        id = this.getHelper().escapeString(id);
        name = this.getHelper().escapeString(name);

        let $container = this.$el.find('.link-teams-container');

        let $el = $('<div />')
            .addClass('link-' + id)
            .addClass('list-group-item');

        $el.html(name + '&nbsp');

        $el.prepend(
            '<a role="button" class="pull-right" data-id="' + id + '" ' +
            'data-action="clearLinkTeams"><span class="fas fa-times"></a>'
        );

        $container.append($el);

        return $el;
    }

    fetchSearch() {
        let type = this.$el.find('select.search-type').val();

        if (type === 'isFromTeams') {
            return {
                type: 'isUserFromTeams',
                field: this.name,
                value: this.searchData.teamIdList,
                data: {
                    type: type,
                    teamIdList: this.searchData.teamIdList,
                    teamNameHash: this.searchData.teamNameHash,
                },
            };
        }

        return super.fetchSearch();
    }
}

export default UserFieldView;
