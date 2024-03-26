

import View from 'view';

class CalendarPage extends View {

    template = 'crm:calendar/calendar-page'

    el = '#main'

    fullCalendarModeList = [
        'month',
        'agendaWeek',
        'agendaDay',
        'basicWeek',
        'basicDay',
        'listWeek',
    ]

    events = {
        
        'click [data-action="createCustomView"]': function () {
            this.createCustomView();
        },
        
        'click [data-action="editCustomView"]': function () {
            this.editCustomView();
        },
    }

    
    shortcutKeys = {
        
        'Home': function (e) {
            this.handleShortcutKeyHome(e);
        },
        
        'Numpad7': function (e) {
            this.handleShortcutKeyHome(e);
        },
        
        'Numpad4': function (e) {
            this.handleShortcutKeyArrowLeft(e);
        },
        
        'Numpad6': function (e) {
            this.handleShortcutKeyArrowRight(e);
        },
        
        'ArrowLeft': function (e) {
            this.handleShortcutKeyArrowLeft(e);
        },
        
        'ArrowRight': function (e) {
            this.handleShortcutKeyArrowRight(e);
        },
        
        'Minus': function (e) {
            this.handleShortcutKeyMinus(e);
        },
        
        'Equal': function (e) {
            this.handleShortcutKeyPlus(e);
        },
        
        'NumpadSubtract': function (e) {
            this.handleShortcutKeyMinus(e);
        },
        
        'NumpadAdd': function (e) {
            this.handleShortcutKeyPlus(e);
        },
        
        'Digit1': function (e) {
            this.handleShortcutKeyDigit(e, 1);
        },
        
        'Digit2': function (e) {
            this.handleShortcutKeyDigit(e, 2);
        },
        
        'Digit3': function (e) {
            this.handleShortcutKeyDigit(e, 3);
        },
        
        'Digit4': function (e) {
            this.handleShortcutKeyDigit(e, 4);
        },
        
        'Digit5': function (e) {
            this.handleShortcutKeyDigit(e, 5);
        },
        
        'Digit6': function (e) {
            this.handleShortcutKeyDigit(e, 6);
        },
        
        'Control+Space': function (e) {
            this.handleShortcutKeyControlSpace(e);
        },
    }

    setup() {
        this.mode = this.mode || this.options.mode || null;
        this.date = this.date || this.options.date || null;

          if (!this.mode) {
            this.mode = this.getStorage().get('state', 'calendarMode') || null;

            if (this.mode && this.mode.indexOf('view-') === 0) {
                let viewId = this.mode.slice(5);
                let calendarViewDataList = this.getPreferences().get('calendarViewDataList') || [];
                let isFound = false;

                calendarViewDataList.forEach(item => {
                    if (item.id === viewId) {
                        isFound = true;
                    }
                });

                if (!isFound) {
                    this.mode = null;
                }

                if (this.options.userId) {
                    this.mode = null;
                }
            }
        }

        this.events['keydown.main'] = e => {
            let key = Espo.Utils.getKeyFromKeyEvent(e);

            if (typeof this.shortcutKeys[key] === 'function') {
                this.shortcutKeys[key].call(this, e.originalEvent);
            }
        }

        if (!this.mode || ~this.fullCalendarModeList.indexOf(this.mode) || this.mode.indexOf('view-') === 0) {
            this.setupCalendar();
        }
        else {
            if (this.mode === 'timeline') {
                this.setupTimeline();
            }
        }
    }

    afterRender() {
        this.$el.focus();
    }

    updateUrl(trigger) {
        let url = '#Calendar/show';

        if (this.mode || this.date) {
            url += '/';
        }

        if (this.mode) {
            url += 'mode=' + this.mode;
        }

        if (this.date) {
            url += '&date=' + this.date;
        }

        if (this.options.userId) {
            url += '&userId=' + this.options.userId;

            if (this.options.userName) {
                url += '&userName=' + encodeURIComponent(this.options.userName);
            }
        }

        this.getRouter().navigate(url, {trigger: trigger});
    }

    setupCalendar() {
        let viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'calendarView']) ||
            'crm:views/calendar/calendar';

        this.createView('calendar', viewName, {
            date: this.date,
            userId: this.options.userId,
            userName: this.options.userName,
            mode: this.mode,
            fullSelector: '#main > .calendar-container',
        }, view => {
            let initial = true;

            this.listenTo(view, 'view', (date, mode) => {
                this.date = date;
                this.mode = mode;

                if (!initial) {
                    this.updateUrl();
                }

                initial = false;
            });

            this.listenTo(view, 'change:mode', (mode, refresh) => {
                this.mode = mode;

                if (!this.options.userId) {
                    this.getStorage().set('state', 'calendarMode', mode);
                }

                if (refresh) {
                    this.updateUrl(true);

                    return;
                }

                if (!~this.fullCalendarModeList.indexOf(mode)) {
                    this.updateUrl(true);
                }

                this.$el.focus();
            });
        });
    }

    setupTimeline() {
        var viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'timelineView']) ||
            'crm:views/calendar/timeline';

        this.createView('calendar', viewName, {
            date: this.date,
            userId: this.options.userId,
            userName: this.options.userName,
            fullSelector: '#main > .calendar-container',
        }, view => {
            let initial = true;

            this.listenTo(view, 'view', (date, mode) => {
                this.date = date;
                this.mode = mode;

                if (!initial) {
                    this.updateUrl();
                }

                initial = false;
            });

            this.listenTo(view, 'change:mode', (mode) => {
                this.mode = mode;

                if (!this.options.userId) {
                    this.getStorage().set('state', 'calendarMode', mode);
                }

                this.updateUrl(true);
            });
        });
    }

    updatePageTitle() {
        this.setPageTitle(this.translate('Calendar', 'scopeNames'));
    }

    createCustomView() {
        this.createView('createCustomView', 'crm:views/calendar/modals/edit-view', {}, (view) => {
            view.render();

            this.listenToOnce(view, 'after:save', (data) => {
                view.close();
                this.mode = 'view-' + data.id;
                this.date = null;

                this.updateUrl(true);
            });
        });
    }

    editCustomView() {
        let viewId = this.getCalendarView().viewId;

        if (!viewId) {
            return;
        }

        this.createView('createCustomView', 'crm:views/calendar/modals/edit-view', {
            id: viewId
        }, (view) => {
            view.render();

            this.listenToOnce(view, 'after:save', () => {
                view.close();

                let calendarView = this.getCalendarView();

                calendarView.setupMode();
                calendarView.reRender();
            });

            this.listenToOnce(view, 'after:remove', () => {
                view.close();

                this.mode = null;
                this.date = null;

                this.updateUrl(true);
            });
        });
    }

    
    getCalendarView() {
        return this.getView('calendar');
    }

    
    handleShortcutKeyHome(e) {
        e.preventDefault();

        this.getCalendarView().actionToday();
    }

    
    handleShortcutKeyArrowLeft(e) {
        e.preventDefault();

        this.getCalendarView().actionPrevious();
    }

    
    handleShortcutKeyArrowRight(e) {
        e.preventDefault();

        this.getCalendarView().actionNext();
    }

    
    handleShortcutKeyMinus(e) {
        if (!this.getCalendarView().actionZoomOut) {
            return;
        }

        e.preventDefault();

        this.getCalendarView().actionZoomOut();
    }

    
    handleShortcutKeyPlus(e) {
        if (!this.getCalendarView().actionZoomIn) {
            return;
        }

        e.preventDefault();

        this.getCalendarView().actionZoomIn();
    }

    
    handleShortcutKeyDigit(e, digit) {
        let modeList = this.getCalendarView().hasView('modeButtons') ?
            this.getCalendarView()
                .getModeButtonsView()
                .getModeDataList(true)
                .map(item => item.mode) :
            this.getCalendarView().modeList;

        let mode = modeList[digit - 1];

        if (!mode) {
            return;
        }

        e.preventDefault();

        if (mode === this.mode) {
            this.getCalendarView().actionRefresh();

            return;
        }

        this.getCalendarView().selectMode(mode);
    }

    
    handleShortcutKeyControlSpace(e) {
        if (!this.getCalendarView().createEvent) {
            return;
        }

        e.preventDefault();

        this.getCalendarView().createEvent();
    }
}


export default CalendarPage;
