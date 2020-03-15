<?php
session_start();
include_once ("functions.php");

if (isset($_GET['lang'])){
    setcookie("lang", $_GET['lang'], time()+3600, '/');
    $_COOKIE['lang'] = $_GET['lang'];
    $lang = $_COOKIE['lang'];
}
if ($_COOKIE["lang"]) {
    $lang = $_COOKIE["lang"];
} else {
    $lang = 'en/';
}

$article_array = getArticleArray($lang);
$per_page = 5;
$num_of_pages = count($article_array) / $per_page;;
$page = (int)(isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
unset($_REQUEST['page']);

$content = "";
$content .= printPreReqs();

$content .= printHeader($lang);
// $content .= printSearch();
$content .= "<div class=\"page\">"; // page div, keeps page minimum height
$content .= printNavigation();
$content .= printAdvert();
$content .= printInfo();
$content .= printContactForm();
$content .= printAdvert();
// $content .= "<div class=\"block2\"></div>";
$content .= "</div>"; // page div, keeps page minimum height
$content .= printFooter();

$content .= printPostReqs();
print $content; // PRINT CONTENT
?>
<script>
</script>
