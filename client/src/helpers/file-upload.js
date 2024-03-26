




class FileUploadExport {

    
    constructor(config) {
        
        this.config = config;
    }

    

    
    upload(file, attachment, options) {
        options = options || {};

        options.afterChunkUpload = options.afterChunkUpload || (() => {});
        options.afterAttachmentSave = options.afterAttachmentSave || (() => {});
        options.mediator = options.mediator || {};

        attachment.set('name', file.name);
        attachment.set('type', file.type || 'text/plain');
        attachment.set('size', file.size);

        if (this._useChunks(file)) {
            return this._uploadByChunks(file, attachment, options);
        }

        return new Promise((resolve, reject) => {
            const fileReader = new FileReader();

            fileReader.onload = (e) => {
                attachment.set('file', e.target.result);

                attachment
                    .save({}, {timeout: 0})
                    .then(() => resolve())
                    .catch(() => reject());
            };

            fileReader.readAsDataURL(file);
        });
    }

    
    _uploadByChunks(file, attachment, options) {
        return new Promise((resolve, reject) => {
            attachment.set('isBeingUploaded', true);

            attachment
                .save()
                .then(() => {
                    options.afterAttachmentSave(attachment);

                    return this._uploadChunks(
                        file,
                        attachment,
                        resolve,
                        reject,
                        options
                    );
                })
                .catch(() => reject());
        });
    }

    
    _uploadChunks(file, attachment, resolve, reject, options, start) {
        start = start || 0;
        let end = start + this._getChunkSize() + 1;

        if (end > file.size) {
            end = file.size;
        }

        if (options.mediator.isCanceled) {
            reject();

            return;
        }

        const blob = file.slice(start, end);

        const fileReader = new FileReader();

        fileReader.onloadend = (e) => {
            if (e.target.readyState !== FileReader.DONE) {
                return;
            }

            Espo.Ajax
                .postRequest('Attachment/chunk/' + attachment.id, e.target.result, {
                    headers: {
                        contentType: 'multipart/form-data',
                    }
                })
                .then(() => {
                    options.afterChunkUpload(end);

                    if (end === file.size) {
                        resolve();

                        return;
                    }

                    this._uploadChunks(
                        file,
                        attachment,
                        resolve,
                        reject,
                        options,
                        end
                    );
                })
                .catch(() => reject());
        };

        fileReader.readAsDataURL(blob);
    }

    
    _useChunks(file) {
        const chunkSize = this._getChunkSize();

        if (!chunkSize) {
            return false;
        }

        if (file.size > chunkSize) {
            return true;
        }

        return false;
    }

    
    _getChunkSize() {
        return (this.config.get('attachmentUploadChunkSize') || 0) * 1024 * 1024;
    }
}

export default FileUploadExport;
