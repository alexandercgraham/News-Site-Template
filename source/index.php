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
if (isset($_GET['page'])){
    $page = $_GET['page'];
}

$content = "";
$content .= printPreReqs();

// $content .= "<div class=\"block2\">";
$content .= printHeader($lang);
$content .= printSearch();
$content .= "<div class=\"page\">"; // page div, keeps page minimum height
$content .= printNavigation();
$content .= printAdvert();
if ($page == 0 && !isset($_REQUEST['search']) && !isset($_REQUEST['tag'])) {
    $content .= printFeatures();
}
// $content .= printPagination($lang, $page, $num_of_pages);
// PRINT PAGINATED ARRAY OF ARTICLES
$article_array = getArticleArray($lang);
if (isset($_REQUEST['search'])) {
    $article_array = getSearch($article_array, $_REQUEST['search']);
    // TODO save $article_array upon page change for paginating tag/searcg results, $unpaginate is temp
    $unpaginate = true;
}
if (isset($_REQUEST['tag'])) {
    $article_array = getTag($article_array, $_REQUEST['tag']);
    // TODO save $article_array upon page change for paginating tag/searcg results, $unpaginate is temp
    $unpaginate = true;
}

$per_page = 5;
$num_of_pages = count($article_array) / $per_page;;
$page = (int)(isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
unset($_REQUEST['page']);

if (empty($article_array)) {
    $content .= "<div class=\"no-articles\">NO ARTICLES FOUND!</div>";
} else {
    // TODO save $article_array upon page change for paginating tag/searcg results, $unpaginate is temp
    if ($unpaginate) {
        $page = 0;
        $num_of_pages = 0;
        $per_page = 99;
    }
    foreach (paginateArray($article_array, $page * $per_page, $per_page) as $article_path) {
        // if (empty($article_array)) {
        //     $content .= "<div>No articles found!</div>";
        //     break;
        // }
        $content .= printArticle($article_path);
    }
}
// if (!$unpaginate) {
    $content .= printPagination($lang, $page, $num_of_pages);
// }
$content .= printAdvert();
$content .= "</div>"; // page div, keeps page minimum height
$content .= printFooter();
$content .= displayArticleDrawer();
// $content .= "</div>";

$content .= "<img id=loading_image src=\"css/loading.gif\" style=margin-left:35%;width:20%;z-index:999;top:35%;position:fixed;background-color:transparent;display:none;pointer-events:none>";

$content .= printPostReqs();
print $content; // PRINT CONTENT
?>
<script>
</script>
