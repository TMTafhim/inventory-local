<style>
.jodit_toolbar_btn-about{
	display:none;
	}
</style>

 
<link rel="stylesheet" href="editor/build/jodit.min.css"/>
<link rel="stylesheet" href="editor/assets/prism.css"/>
<link rel="stylesheet" href="editor/assets/app.css"/>

<script src="editor/build/jodit.min.js"></script>
<script src="editor/assets/prism.js"></script>
<script src="editor/assets/app.js"></script>
<script>
var editor = new Jodit("#area_editor", {
  "autofocus": true,
  "iframe": true,
  "uploader": {
    "insertImageAsBase64URI": true
  },
  "spellcheck": false
});
</script>


<script>
var editor = new Jodit("#area_editor2", {
  "autofocus": true,
  "iframe": true,
  "uploader": {
    "insertImageAsBase64URI": true
  },
  "spellcheck": false
});
</script>
