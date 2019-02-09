
/* ---------------------------------------------------------------------------
 * Wysiwym Markdown
 * Markdown markup language for the Wysiwym editor
 * Reference: http://daringfireball.net/projects/markdown/syntax
 *---------------------------------------------------------------------------- */
Wysiwym.Markdown = function(textarea) {
    this.textarea = textarea;    // jQuery textarea object

    // Initialize the Markdown Buttons
    this.buttons = [
        new Wysiwym.Button('<b>B</b>',   Wysiwym.span,  {prefix:'**', suffix:'**', text:'strong text', show: true }),
        new Wysiwym.Button('<i>I</i>', Wysiwym.span,  {prefix:'_',  suffix:'_',  text:'italic text', show: true}),
        '|',
        new Wysiwym.Button('<b>H1</b>',   Wysiwym.span,  {prefix:'# ',  suffix:'', text:''   , show: true}),
        new Wysiwym.Button('<b>H2</b>',   Wysiwym.span,  {prefix:'## ',  suffix:'', text:''  , show: true}),
        new Wysiwym.Button('<b>H3</b>',   Wysiwym.span,  {prefix:'### ',  suffix:'', text:'' , show: true}),
        new Wysiwym.Button('<b>H4</b>',   Wysiwym.span,  {prefix:'#### ',  suffix:'', text:'', show: true}),
        '|',
        new Wysiwym.Button('link',   Wysiwym.span,  {prefix:'[',  suffix:']()', text:'link text'}),
        new Wysiwym.Button('image',   Wysiwym.span,  {prefix:'![',  suffix:']()', text:'link text'}),
        new Wysiwym.Button('audio',   Wysiwym.span,  {prefix:'[audio=',  suffix:']', text:''}),
        new Wysiwym.Button('video',   Wysiwym.span,  {prefix:'[video=',  suffix:']', text:''}),
        '|',
        new Wysiwym.Button('ul', Wysiwym.list, {prefix:'* ', wrap:true}),
        new Wysiwym.Button('ol', Wysiwym.list, {prefix:'0. ', wrap:true, regex:/^\s*\d+\.\s/}),
        '|',
        new Wysiwym.Button('pre',   Wysiwym.block, {prefix:'    ', wrap:true}),
        new Wysiwym.Button('code',   Wysiwym.span, {prefix:'`', suffix:'`'}),
        new Wysiwym.Button('quote',  Wysiwym.list,  {prefix:'> ',   wrap:true})
    ];

    // Configure auto-indenting
    this.exitindentblankline = true;    // True to insert blank line when exiting auto-indent ;)
    this.autoindents = [                // Regex lookups for auto-indent
        /^\s*\*\s/,                     // Bullet list
        /^\s*(\d+)\.\s/,                // Number list (number selected for auto-increment)
        /^\s*\>\s/,                     // Quote list
        /^\s{4}\s*/                     // Code block
    ];

    // Syntax items to display in the help box
    this.help = [
        { label: 'Header', syntax: '## Header ##' },
        { label: 'Bold',   syntax: '**bold**' },
        { label: 'Italic', syntax: '_italics_' },
        { label: 'Link',   syntax: '[pk!](http://google.com)' },
        { label: 'Bullet List', syntax: '* list item' },
        { label: 'Number List', syntax: '1. list item' },
        { label: 'Blockquote', syntax: '&gt; quoted text' },
        { label: 'Large Code Block', syntax: '(Begin lines with 4 spaces)' },
        { label: 'Inline Code Block', syntax: '&lt;code&gt;inline code&lt;/code&gt;' }
    ];
};
