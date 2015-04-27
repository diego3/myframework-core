Fileupload = {
    /*
     * Element that contais the popup
     * @type Element
     */
    popup: null,
    
    /**
     * Lista dos arquivos que será enviado
     * @type Array
     */
    files: [],
    
    /**
     * Define se o arquivo será enviado automaticamente ou não
     * @type Boolean
     */
    autosumit: false,
    
    /**
     * Elemento utilizado para abrir o popup
     * @type Element
     */
    openElement: '',
    
    /**
     * Define se o plugin está sendo usado no modo popup
     */
    isPopupMode: function () {
        return Fileupload.popup !== null;
    },
    
    preloadPopup: function () {
        this.popup = $('#myFileUpload');
        if (this.popup.length == 0) {
            $('body').append('<div class="modal fade" id="myFileUpload" tabindex="-1" role="dialog" aria-hidden="true">' +
                '<div class="modal-dialog modal-lg"><div class="modal-content">' +
                '<div class="modal-footer">&nbsp;</div></div></div></div>'
            );
            this.popup = $('#myFileUpload');
        }
        this.popup.on('loaded.bs.modal', function () {
            Fileupload.popup.find("input[type=file]").fileinput();
            Fileupload.prepareAjaxupload(Fileupload.popup);
            Fileupload.popup.find(".lista-imagem figure").click(function (){
                Fileupload.selectImage($(this).find('img').attr('src'));
            });
        });
    },
    
    prepareAjaxupload: function (context) {
        $(context).find('input[type=file]').on('change', function (event){
            Fileupload.files = event.target.files;
            if (Fileupload.autosubmit) {
                $(this).closest('form').find('button[type=submit]').click();
            }
        });
        //http://abandon.ie/notebook/simple-file-uploads-using-jquery-ajax
        $(context).find('form.form-upload').on('submit', function (event){
            event.stopPropagation();
            event.preventDefault();
 
            if (Fileupload.files.length === 0) {
                return;
            }
            // START A LOADING SPINNER HERE
            var data = new FormData();
            $.each(Fileupload.files, function(key, value) {
		data.append(key, value);
            });
            var path = $('code[data-path]').data('path');
            data.append('path', path);
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR) {
                    if (typeof data.files === 'object' && data.files.length > 0) {
                        if (Fileupload.isPopupMode() && data.files.length === 1) {
                            Fileupload.selectImage(path + data.files[0]);
                        }
                        $('input[type=file]').fileinput('clear');
                        $.each(data.files, function (i, imgname) {
                            Fileupload.appendImage(imgname);
                        });
                    }
                    if (data.error.length > 0) {
        		// Handle errors here
        		console.log('ERRORS 1: ' + data.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle errors here
                    console.log("jqXHR ", jqXHR.responseText);
                    console.log('ERRORS 2: ' + textStatus);
                    // STOP LOADING SPINNER
                }
            });
        });
    },
    
    appendImage: function (imgname) {
        //Remove image if exists in filechoose
        var container = $('.lista-imagem');
        if (container.length === 0) {            
            $('#maincontent').append('<div class="file-preview-thumbnails lista-imagem" data-path="'+$('code[data-path]').data('path')+'"></div>')
            var container = $('.lista-imagem');
        }
        container.append('<div class="file-preview-frame"><figure data-name="' + imgname + '">' +
            '<img src="' + container.data('path') + '/' +imgname + '" class="file-preview-image">' +
            '<figcaption>' + imgname + '</figcaption></figure></div>'
        );
        if (Fileupload.isPopupMode()) {
            container.find('figure[data-name="' + imgname +  '"]').click(function (){
                Fileupload.selectImage(container.data('path') + '/' +imgname);
            });
        }
    },
    
    selectImage: function (image) {        
        if (Fileupload.openElement) {
            console.log(Fileupload.openElement);
            var imgid = '#' + Fileupload.openElement.data('imgid');
            var hidden = '#' + Fileupload.openElement.data('hiddenid');
            $(imgid).attr('src', image);
            $(hidden).attr('value', image);
            Fileupload.popup.modal('hide');
        }
    }
};

$(function (){
   Fileupload.preloadPopup();
   Fileupload.prepareAjaxupload('body');
   
   //Prepare links
   $('a[data-up-action="fileupload"]').click(function (){
       //TODO add random parameter to avoid cache
       Fileupload.openElement = $(this);
   });
});