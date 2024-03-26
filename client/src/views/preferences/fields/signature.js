

define('views/preferences/fields/signature', ['views/fields/wysiwyg'], function (Dep) {

    return Dep.extend({

        fetchEmptyValueAsNull: true,

        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["color", ["color"]],
            ["height", ["height"]],
            ['table', ['espoLink']],
            ["misc",["codeview", "fullscreen"]],
        ],
    });
});
