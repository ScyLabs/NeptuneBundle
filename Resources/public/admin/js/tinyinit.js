tinyMCE.init({
    language : "fr_FR",
    selector: 'textarea.tiny',
    height: 300,
    menubar: false,
    plugins: [
        'advlist autolink lists link charmap print preview anchor code codesample textcolor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime table contextmenu paste code help wordcount'
    ],
    toolbar: 'insert | undo redo  | bold italic  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | codesample | help',
    content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        '//www.tinymce.com/css/codepen.min.css']
});