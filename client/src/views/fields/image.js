

import FileFieldView from 'views/fields/file';

class ImageFieldView extends FileFieldView {

    type = 'image'

    showPreview = true
    accept = ['image/*']
    defaultType = 'image/jpeg'
    previewSize = 'small'
}

export default ImageFieldView;
