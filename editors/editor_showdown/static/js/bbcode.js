
/* ---------------------------------------------------------------------------
 * Wysiwym BBCode
 * BBCode markup language for the Wysiwym editor
 * Reference: http://labs.spaceshipnofuture.org/icky/help/formatting/
 *---------------------------------------------------------------------------- */
Wysiwym.BBCode = function(textarea) {
    this.textarea = textarea;    // jQuery textarea object

    // Initialize the Markdown Buttons

    this.buttons = [
        new Wysiwym.Button('<b>B</b>',   Wysiwym.span,  {prefix:'[b]', suffix:'[/b]', text:'strong text', show: true }),
        new Wysiwym.Button('<i>I</i>', Wysiwym.span,  {prefix:'[i]',  suffix:'[i]',  text:'italic text', show: true}),
        '|',
        new Wysiwym.Button('<b>H1</b>',   Wysiwym.span,  {prefix:'[h1]',  suffix:'[/h1]', text:''   , show: true}),
        new Wysiwym.Button('<b>H2</b>',   Wysiwym.span,  {prefix:'[h2]',  suffix:'[/h2]', text:''  , show: true}),
        new Wysiwym.Button('<b>H3</b>',   Wysiwym.span,  {prefix:'[h3]',  suffix:'[/h3]', text:'' , show: true}),
        new Wysiwym.Button('<b>H4</b>',   Wysiwym.span,  {prefix:'[h4]',  suffix:'[/h4]', text:'', show: true}),
        '|',
        new Wysiwym.Button('link',   Wysiwym.span,  {prefix:'[url=]',  suffix:'[/url]', text:'link text'}),
        new Wysiwym.Button('image',   Wysiwym.span,  {prefix:'[img=]',  suffix:'[/img]', text:'link text'}),
        new Wysiwym.Button('audio',   Wysiwym.span,  {prefix:'[audio=',  suffix:']', text:''}),
        new Wysiwym.Button('video',   Wysiwym.span,  {prefix:'[video=',  suffix:']', text:''}),
        '|',
        new Wysiwym.Button('pre',   Wysiwym.span, {prefix:'[code]', suffix: '[/code]', wrap:true}),
        new Wysiwym.Button('quote',  Wysiwym.span, {prefix:'[quote]', suffix: '[/quote]',   wrap:true})
    ];

    // Syntax items to display in the help box
    this.help = [
        { label: 'Bold',   syntax: "[b]bold[/b]" },
        { label: 'Italic', syntax: "[i]italics[/i]" },
        { label: 'Link',   syntax: '[url="http://example.com"]pk![/url]' },
        { label: 'Blockquote', syntax: '[quote]quote text[/quote]' },
        { label: 'Large Code Block', syntax: '[code]code text[/code]' }
    ];
};
