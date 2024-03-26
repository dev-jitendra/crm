

define('crm:views/dashlets/calendar', ['views/dashlets/abstract/base'], function (Dep) {

    return Dep.extend({

        name: 'Calendar',

        noPadding: true,

        templateContent: '<div class="calendar-container">{{{calendar}}} </div>',

        init: function () {
            Dep.prototype.init.call(this);
        },

        afterRender: function () {
            var mode = this.getOption('mode');

            if (mode === 'timeline') {
                var userList = [];
                var userIdList = this.getOption('usersIds') || [];
                var userNames = this.getOption('usersNames') || {};

                userIdList.forEach(id => {
                    userList.push({
                        id: id,
                        name: userNames[id] || id,
                    });
                });

                let viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'timelineView']) ||
                    'crm:views/calendar/timeline';

                this.createView('calendar', viewName, {
                    selector: '> .calendar-container',
                    header: false,
                    calendarType: 'shared',
                    userList: userList,
                    enabledScopeList: this.getOption('enabledScopeList'),
                    noFetchLoadingMessage: true,
                }, (view) => {
                    view.render();
                });

                return;
            }

            var teamIdList = null;

            if (~['basicWeek', 'month', 'basicDay'].indexOf(mode)) {
                teamIdList = this.getOption('teamsIds');
            }

            let viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'calendarView']) ||
                'crm:views/calendar/calendar';

            this.createView('calendar', viewName, {
                mode: mode,
                selector: '> .calendar-container',
                header: false,
                enabledScopeList: this.getOption('enabledScopeList'),
                containerSelector: this.getSelector(),
                teamIdList: teamIdList,
            }, view => {
                this.listenTo(view, 'view', () => {
                    if (this.getOption('mode') === 'month') {
                        let title = this.getOption('title');

                        let $container = $('<span>')
                            .append(
                                $('<span>').text(title),
                                ' <span class="chevron-right"></span> ',
                                $('<span>').text(view.getTitle())
                            );

                        let $headerSpan = this.$el.closest('.panel').find('.panel-heading > .panel-title > span');

                        $headerSpan.html($container.get(0).innerHTML);
                    }
                });

                view.render();

                this.on('resize', () => {
                    setTimeout(() => view.adjustSize(), 50);
                });
            });
        },

        setupActionList: function () {
            this.actionList.unshift({
                name: 'viewCalendar',
                text: this.translate('View Calendar', 'labels', 'Calendar'),
                url: '#Calendar',
                iconHtml: '<span class="far fa-calendar-alt"></span>',
                onClick: () => this.actionViewCalendar(),
            });
        },

        setupButtonList: function () {
            if (this.getOption('mode') !== 'timeline') {
                this.buttonList.push({
                    name: 'previous',
                    html: '<span class="fas fa-chevron-left"></span>',
                    onClick: () => this.actionPrevious(),
                });

                this.buttonList.push({
                    name: 'next',
                    html: '<span class="fas fa-chevron-right"></span>',
                    onClick: () => this.actionNext(),
                });
            }
        },

        actionRefresh: function () {
            var view = this.getView('calendar');

            if (!view) {
                return;
            }

            view.actionRefresh();
        },

        actionNext: function () {
            var view = this.getView('calendar');

            if (!view) {
                return;
            }

            view.actionNext();
        },

        actionPrevious: function () {
            var view = this.getView('calendar');

            if (!view) {
                return;
            }

            view.actionPrevious();
        },

        actionViewCalendar: function () {
            this.getRouter().navigate('#Calendar', {trigger: true});
        },
    });
});
