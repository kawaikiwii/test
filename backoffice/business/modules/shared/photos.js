<script type="text/javascript">
    CropImageManager.init($('original').value);
	
    bizCreatePhoto = function (responseElement) {
		wcmBizAjaxController.call("biz.createPhoto", {
			responseElement : responseElement,
			credits : $("credits").value,
			keywords: $("keywords").value,
			title: $("content_photo_title").value,
			description: tinyMCE.editors.content_photo_description.getContent(),
			text: tinyMCE.editors.content_photo_text.getContent()
			
			});
		
	};
</script>
