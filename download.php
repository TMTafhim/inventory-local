<?php

// place this code inside a php file and call it f.e. "download.php"
// $path = $_SERVER['DOCUMENT_ROOT']."/path2file/"; // change the path to fit your websites document structure
$path = isset($_GET['path']) ? $_GET['path'] : '';
$downloadFile = isset($_GET['download_file']) ? basename($_GET['download_file']) : '';
$pathAliases = array(
    'RequisitionAttachment/' => 'RequistionAttachment/',
    'RequisitionAttachment' => 'RequistionAttachment/'
);
if(isset($pathAliases[$path])){
    $path = $pathAliases[$path];
}
$path = rtrim($path, '/').'/';
$fullPath = $path.$downloadFile;

if (empty($downloadFile) || !is_file($fullPath)) {
    http_response_code(404);
    echo "File not found.";
    exit;
}

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default:
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
    fclose ($fd);
    exit;
}

http_response_code(404);
echo "File not found.";
exit;
// example: place this kind of link into the document where the file download is offered:
// <a href="download.php?download_file=some_file.pdf">Download here</a>
?>

