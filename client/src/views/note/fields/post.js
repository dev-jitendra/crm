

define('views/note/fields/post', ['views/fields/text', 'lib!jquery-textcomplete'], function (Dep, Textcomplete) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.events['paste textarea'] = e => this.handlePaste(e);

            this.insertedImagesData = {};
        },

        handlePaste: function (e) {
            if (!e.originalEvent.clipboardData) {
                return;
            }

            let text = e.originalEvent.clipboardData.getData('text/plain');

            if (!text) {
                return;
            }

            text = text.trim();

            if (!text) {
                return;
            }

            this.handlePastedText(text, e.originalEvent);
        },

        afterRenderEdit: function () {
            let placeholderText = this.options.placeholderText ||
                this.translate('writeMessage', 'messages', 'Note');

            this.$element.attr('placeholder', placeholderText);

            this.$textarea = this.$element;

            let $textarea = this.$textarea;

            $textarea.off('drop');
            $textarea.off('dragover');
            $textarea.off('dragleave');
            $textarea.off('paste');

            $textarea.on('paste', (e) => {
                var items = e.originalEvent.clipboardData.items;

                if (items) {
                    for (var i = 0; i < items.length; i++) {
                        if (!~items[i].type.indexOf('image')) {
                            continue;
                        }

                        var blob = items[i].getAsFile();

                        this.trigger('add-files', [blob]);
                    }
                }
            });

            this.$textarea.on('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();

                e = e.originalEvent;

                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    this.trigger('add-files', e.dataTransfer.files);
                }

                this.$textarea.attr('placeholder', originalPlaceholderText);
            });

            let originalPlaceholderText = this.$textarea.attr('placeholder');

            this.$textarea.on('dragover', e => {
                e.preventDefault();

                this.$textarea.attr('placeholder', this.translate('dropToAttach', 'messages'));
            });

            this.$textarea.on('dragleave', e => {
                e.preventDefault();

                this.$textarea.attr('placeholder', originalPlaceholderText);
            });

            let assignmentPermission = this.getAcl().get('assignmentPermission');

            var buildUserListUrl = term => {
                let url = 'User?q=' + term + '&' + $.param({'primaryFilter': 'active'}) +
                    '&orderBy=name&maxSize=' + this.getConfig().get('recordsPerPage') +
                    '&select=id,name,userName';

                if (assignmentPermission === 'team') {
                    url += '&' + $.param({'boolFilterList': ['onlyMyTeam']})
                }

                return url;
            };

            if (assignmentPermission !== 'no' && this.model.isNew()) {
                this.$element.textcomplete([{
                    match: /(^|\s)@(\w*)$/,
                    search: (term, callback) => {
                        if (term.length === 0) {
                            callback([]);

                            return;
                        }

                        Espo.Ajax
                            .getRequest(buildUserListUrl(term))
                            .then(data => {
                                callback(data.list)
                            });
                    },
                    template: mention => {
                        return this.getHelper().escapeString(mention.name) +
                            ' <span class="text-muted">@' +
                            this.getHelper().escapeString(mention.userName) + '</span>';
                    },
                    replace: o => {
                        return '$1@' + o.userName + '';
                    },
                }],{zIndex: 1100});

                this.once('remove', () => {
                    if (this.$element.length) {
                        this.$element.textcomplete('destroy');
                    }
                });
            }
        },

        validateRequired: function () {
            if (this.isRequired()) {
                if ((this.model.get('attachmentsIds') || []).length) {
                    return false;
                }
            }

            return Dep.prototype.validateRequired.call(this);
        },

        handlePastedText: function (text, event) {
            if (!(/^http(s){0,1}\:\/\
                return;
            }

            let imageExtensionList = ['jpg', 'jpeg', 'png', 'gif'];
            let regExpString = '.+\\.(' + imageExtensionList.join('|') + ')(/?.*){0,1}$';
            let regExp = new RegExp(regExpString, 'i');
            let url = text;
            let siteUrl = this.getConfig().get('siteUrl').replace(/\/$/, '');

            let attachmentIdList = this.model.get('attachmentsIds') || [];

            if (regExp.test(text)) {
                let insertedId = this.insertedImagesData[url];

                if (insertedId) {
                    if (~attachmentIdList.indexOf(insertedId)) {
                        return;
                    }
                }

                Espo.Ajax
                    .postRequest('Attachment/fromImageUrl', {
                        url: url,
                        parentType: 'Note',
                        field: 'attachments',
                    })
                    .then(attachment => {
                        let attachmentIdList = Espo.Utils.clone(this.model.get('attachmentsIds') || []);
                        let attachmentNames = Espo.Utils.clone(this.model.get('attachmentsNames') || {});
                        let attachmentTypes = Espo.Utils.clone(this.model.get('attachmentsTypes') || {});

                        attachmentIdList.push(attachment.id);
                        attachmentNames[attachment.id] = attachment.name;
                        attachmentTypes[attachment.id] = attachment.type;

                        this.insertedImagesData[url] = attachment.id;

                        this.model.set({
                            attachmentsIds: attachmentIdList,
                            attachmentsNames: attachmentNames,
                            attachmentsTypes: attachmentTypes,
                        });
                    })
                    .catch(xhr => {
                        xhr.errorIsHandled = true;
                    });

                return;
            }

            if (/\?entryPoint\=image\&/.test(text) && text.indexOf(siteUrl) === 0) {
                url = text.replace(/[\&]{0,1}size\=[a-z\-]*/, '');

                let match = /\&{0,1}id\=([a-z0-9A-Z]*)/g.exec(text)

                if (match.length !== 2) {
                    return;
                }

                let id = match[1];

                if (~attachmentIdList.indexOf(id)) {
                    return;
                }

                let insertedId = this.insertedImagesData[id];

                if (insertedId) {
                    if (~attachmentIdList.indexOf(insertedId)) {
                        return;
                    }
                }

                Espo.Ajax
                    .postRequest('Attachment/copy/' + id, {
                        parentType: 'Note',
                        field: 'attachments',
                    })
                    .then(attachment => {
                        let attachmentIdList = Espo.Utils.clone(this.model.get('attachmentsIds') || []);
                        let attachmentNames = Espo.Utils.clone(this.model.get('attachmentsNames') || {});
                        let attachmentTypes = Espo.Utils.clone(this.model.get('attachmentsTypes') || {});

                        attachmentIdList.push(attachment.id);
                        attachmentNames[attachment.id] = attachment.name;
                        attachmentTypes[attachment.id] = attachment.type;

                        this.insertedImagesData[id] = attachment.id;

                        this.model.set({
                            attachmentsIds: attachmentIdList,
                            attachmentsNames: attachmentNames,
                            attachmentsTypes: attachmentTypes,
                        });
                    })
                    .catch(xhr => {
                        xhr.errorIsHandled = true;
                    });
            }
        },
    });
});
