<style>
.jodit_toolbar_btn-about{
	display:none;
	}
</style>
<textarea class="area_editor" ></textarea>
<textarea id="area_editor2" ></textarea>
 
<link rel="stylesheet" href="build/jodit.min.css"/>
<link rel="stylesheet" href="assets/prism.css"/>
<link rel="stylesheet" href="assets/app.css"/>
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,700,700i" rel="stylesheet">

<script src="build/jodit.min.js"></script>
<script src="assets/prism.js"></script>
<script src="assets/app.js"></script>
<script>
var editor = new Jodit(".area_editor", {
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