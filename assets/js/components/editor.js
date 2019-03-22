import home from "tui-editor/dist/tui-editor.png";

const Editor = require('tui-editor');

const target = $('#markdown_editor').data('target');
const initialContent = $('[name="' + target + '"]').val();
console.log(initialContent);

const editor = new Editor({
    el: document.querySelector('#markdown_editor'),
    initialEditType: 'markdown',
    previewStyle: 'vertical',
    height: '300px',
    initialValue: initialContent,
    useDefaultHTMLSanitizer: false,
    events: {
        blur: function(blur){ 
            console.log('blur triggered');
            const content = editor.getMarkdown();
            const target = $('#markdown_editor').data('target');
            $('[name="' + target + '"]').val(content);
        }
      },
});
