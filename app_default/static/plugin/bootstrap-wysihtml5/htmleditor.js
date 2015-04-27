$(function (){
   var base = $('base').attr('href');
   if (!base) {
       base = '';
   }
   $('textarea.htmleditor').each(function(){
       $(this).wysihtml5({
            stylesheets: [base + "static/plugin/bootstrap-wysihtml5/lib/css/wysiwyg-color.css"],
            locale: "pt-BR",
            lists: true,
            html: false,
            link: true,
            image: true,
            color: true
       });
   });
});

