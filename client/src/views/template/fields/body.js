

import WysiwygFieldView from 'views/fields/wysiwyg';

class BodyTemplateFieldView extends WysiwygFieldView {

    htmlPurificationForEditDisabled = true
    noStylesheet = true
    useIframe = true
    tableClassName = ''
    tableBorderWidth = 1
    tableCellPadding = 2
}

export default BodyTemplateFieldView;

