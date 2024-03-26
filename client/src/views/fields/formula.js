

import TextFieldView from 'views/fields/text';


let ace;

class FormulaFieldView extends TextFieldView {

    detailTemplate ='fields/formula/detail'
    editTemplate = 'fields/formula/edit'

    height = 300
    maxLineDetailCount = 80
    maxLineEditCount = 200
    insertDisabled = false
    checkSyntaxDisabled = false

    events = {
        
        'click [data-action="addAttribute"]': function () {
            this.addAttribute();
        },
        
        'click [data-action="addFunction"]': function () {
            this.addFunction();
        },
        
        'click [data-action="checkSyntax"]': function () {
            this.checkSyntax();
        },
    }

    setup() {
        super.setup();

        this.height = this.options.height || this.params.height || this.height;

        this.maxLineDetailCount =
            this.options.maxLineDetailCount ||
            this.params.maxLineDetailCount ||
            this.maxLineDetailCount;

        this.maxLineEditCount =
            this.options.maxLineEditCount ||
            this.params.maxLineEditCount ||
            this.maxLineEditCount;

        this.targetEntityType =
            this.options.targetEntityType ||
            this.params.targetEntityType ||
            this.targetEntityType;

        this.insertDisabled = this.insertDisabled || this.options.insertDisabled;
        this.checkSyntaxDisabled = this.checkSyntaxDisabled || this.options.checkSyntaxDisabled;

        this.containerId = 'editor-' + Math.floor((Math.random() * 10000) + 1).toString();

        if (this.mode === this.MODE_EDIT || this.mode === this.MODE_DETAIL) {
            this.wait(
                this.requireAce()
            );
        }

        this.on('remove', () => {
            if (this.editor) {
                this.editor.destroy();
            }
        });
    }

    requireAce() {
        return Espo.loader.requirePromise('lib!ace')
            .then(lib => {
                ace = lib;

                let list = [
                    Espo.loader.requirePromise('lib!ace-mode-javascript'),
                    Espo.loader.requirePromise('lib!ace-ext-language_tools'),
                ];

                if (this.getThemeManager().getParam('isDark')) {
                    list.push(
                        Espo.loader.requirePromise('lib!ace-theme-tomorrow_night')
                    );
                }

                return Promise.all(list);
            });
    }

    data() {
        let data = super.data();

        data.containerId = this.containerId;
        data.targetEntityType = this.targetEntityType;
        data.hasSide = !this.insertDisabled || !this.checkSyntaxDisabled;
        data.hasInsert = !this.insertDisabled;
        data.hasCheckSyntax = !this.checkSyntaxDisabled;

        return data;
    }

    afterRender() {
        super.afterRender();

        this.$editor = this.$el.find('#' + this.containerId);

        if (
            this.$editor.length &&
            (
                this.mode === this.MODE_EDIT ||
                this.mode === this.MODE_DETAIL ||
                this.mode === this.MODE_LIST
            )
        ) {
            this.$editor.css('fontSize', '14px');

            if (this.mode === this.MODE_EDIT) {
                this.$editor.css('minHeight', this.height + 'px');
            }

            let editor = this.editor = ace.edit(this.containerId);

            editor.setOptions({
                maxLines: this.mode === this.MODE_EDIT ?
                    this.maxLineEditCount :
                    this.maxLineDetailCount,
            });

            if (this.getThemeManager().getParam('isDark')) {
                editor.setOptions({
                    theme: 'ace/theme/tomorrow_night',
                });
            }

            if (this.isEditMode()) {
                editor.getSession().on('change', () => {
                    this.trigger('change', {ui: true});
                });

                editor.getSession().setUseWrapMode(true);
            }

            if (this.isReadMode()) {
                editor.setReadOnly(true);
                editor.renderer.$cursorLayer.element.style.display = "none";
                editor.renderer.setShowGutter(false);
            }

            editor.setShowPrintMargin(false);
            editor.getSession().setUseWorker(false);
            editor.commands.removeCommand('find');
            editor.setHighlightActiveLine(false);

            let JavaScriptMode = ace.require('ace/mode/javascript').Mode;

            editor.session.setMode(new JavaScriptMode());

            if (!this.insertDisabled && !this.isReadMode()) {
                this.initAutocomplete();
            }
        }
    }

    fetch() {
        let data = {};

        let value = this.editor.getValue();

        if (value === '') {
            value = null;
        }

        data[this.name] = value;

        return data;
    }

    addAttribute() {
        this.createView('dialog', 'views/admin/formula/modals/add-attribute', {
            scope: this.targetEntityType,
        }, view => {
            view.render();

            this.listenToOnce(view, 'add', (attribute) => {
                this.editor.insert(attribute);

                this.clearView('dialog');
            });
        });
    }

    addFunction() {
        this.createView('dialog', 'views/admin/formula/modals/add-function', {
            scope: this.targetEntityType,
            functionDataList: this.getFunctionDataList(),
        }, view => {
            view.render();

            this.listenToOnce(view, 'add', (string) => {
                this.editor.insert(string);

                this.clearView('dialog');
            });
        });
    }

    getFunctionDataList() {
        let list = Espo.Utils.clone(
            this.getMetadata().get(['app', 'formula', 'functionList']) || []
        );

        if (this.options.additionalFunctionDataList) {
            list = list.concat(this.options.additionalFunctionDataList);
        }

        let allowedFunctionList = this.options.allowedFunctionList;

        if (allowedFunctionList) {
            list = list.filter(item => {
                for (let func of allowedFunctionList) {
                    if (func.endsWith('\\') && item.name.startsWith(func)) {
                        return true;
                    }

                    if (item.name === func) {
                        return true;
                    }
                }

                return false;
            });
        }

        if (!this.targetEntityType) {
            list = list.filter(item => {
                if (item.name.indexOf('entity\\') === 0) {
                    return false;
                }

                return true;
            });
        }

        return list;
    }

    initAutocomplete() {
        let functionItemList = this.getFunctionDataList().filter(item => item.insertText);

        let attributeList = this.getFormulaAttributeList();

        ace.require('ace/ext/language_tools');

        this.editor.setOptions({
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
        });

        
        let completer = {
            identifierRegexps: [/[\\a-zA-Z0-9{}\[\].$'"]/],

            getCompletions: function (editor, session, pos, prefix, callback) {
                let matchedFunctionItemList = functionItemList
                    .filter((originalItem) => {
                        let text = originalItem.name;

                        if (text.indexOf(prefix) === 0) {
                            return true;
                        }

                        let parts = text.split('\\');

                        if (parts[parts.length - 1].indexOf(prefix) === 0) {
                            return true;
                        }

                        return false;
                    });

                let itemList = matchedFunctionItemList.map((item) => {
                    return {
                        caption: item.name + '()',
                        value: item.insertText,
                        meta: item.returnType || null,
                        completer: {
                            insertMatch: (editor, data) => {
                                editor.completer.insertMatch({value: data.value});

                                let index = data.value.indexOf('(');

                                if (!~index) {
                                    return;
                                }

                                if (~data.value.indexOf('()')) {
                                    return;
                                }

                                let pos = editor.selection.getCursor();

                                editor.gotoLine(
                                    pos.row + 1,
                                    pos.column - data.value.length + index + 1
                                );
                            },
                        },
                    };
                });

                let matchedAttributeList = attributeList
                    .filter((item) => {
                        if (item.indexOf(prefix) === 0) {
                            return true;
                        }

                        return false;
                    });

                let itemAttributeList = matchedAttributeList.map((item) => {
                    return {
                        name: item,
                        value: item,
                        meta: 'attribute',
                    };
                });

                itemList = itemList.concat(itemAttributeList);

                callback(null, itemList);
            }
        };

        this.editor.completers = [completer];
    }

    getFormulaAttributeList() {
        if (!this.targetEntityType) {
            return [];
        }

        let attributeList = this.getFieldManager()
            .getEntityTypeAttributeList(this.targetEntityType)
            .concat(['id'])
            .sort();

        let links = this.getMetadata().get(['entityDefs', this.targetEntityType, 'links']) || {};

        let linkList = [];

        Object.keys(links).forEach((link) => {
            let type = links[link].type;

            if (!type) {
                return;
            }

            if (~['belongsToParent', 'hasOne', 'belongsTo'].indexOf(type)) {
                linkList.push(link);
            }
        });

        linkList.sort();

        linkList.forEach((link) => {
            let scope = links[link].entity;

            if (!scope) {
                return;
            }

            if (links[link].disabled) {
                return;
            }

            let linkAttributeList = this.getFieldManager()
                .getEntityTypeAttributeList(scope)
                .sort();

            linkAttributeList.forEach((item) => {
                attributeList.push(link + '.' + item);
            });
        });

        return attributeList;
    }

    checkSyntax() {
        let expression = this.editor.getValue();

        if (!expression) {
            Espo.Ui.success(
                this.translate('checkSyntaxSuccess', 'messages', 'Formula')
            );

            return;
        }

        Espo.Ajax
            .postRequest('Formula/action/checkSyntax', {expression: expression})
            .then(response => {
                if (response.isSuccess) {
                    Espo.Ui.success(
                        this.translate('checkSyntaxSuccess', 'messages', 'Formula')
                    );

                    return;
                }

                let message = this.translate('checkSyntaxError', 'messages', 'Formula');

                if (response.message) {
                    message += ' ' + response.message;
                }

                Espo.Ui.error(message);
            });
    }
}

export default FormulaFieldView;
