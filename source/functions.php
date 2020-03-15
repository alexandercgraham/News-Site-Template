<?php
function printPreReqs() {
    $content = "
        <html>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" />
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>
        <script src=\"functions.js\"></script>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"css/main.css\" media=\"screen\" />
        <body>
    ";
    return $content;
}
function printPostReqs() {
    $content = "
        </body></html>
    ";
    return $content;
}

function getArticleArray($lang) {
    $articles = scandir($lang);
    $article_array = array();
    foreach ($articles as $article) {
        if (strpos($article, '.txt')) {
            array_push($article_array, $lang.$article);
        }
    }
    return $article_array;
}
function getArticleData($article_path, $article_tag, $exception) {
    $article = file_get_contents($article_path);
    $article_data = substr($article, strpos($article, '<'.$article_tag.'>') + strlen('<'.$article_tag.'>'));
    $article_data = substr($article_data, 0, strpos($article_data, '</'.$article_tag.'>'));
    if (!$article_data) {
        return "404: ".$article_tag;
    }
    if ($article_tag == "tags" && !$exception) {
        $tags = explode(",", $article_data);
        $article_data = "<div class=\"tag-links\" id=\"tag-links\">";
        foreach ($tags as $tag) {
            $article_data .= "<a style=\"color:white;background: rgba(0, 0, 0, 0.1);border-radius: 3px;\" href=\"?tag=".str_replace(" ", "_", $tag)."\">&nbsp;".trim($tag)."&nbsp;</a>&nbsp;";
        }
        $article_data .= "</div>";
    }
    return $article_data;
}
function getArticleInlineData($article_path, $article_tag) {
    $article_data = substr($article, strpos($article, '<'.$article_tag.'>') + strlen('<'.$article_tag.'>'));
    $article_data = substr($article_data, 0, strpos($article_data, '</'.$article_tag.'>'));
    if (!$article_data) {
        $article_data = "404: ".$article_tag."!";
    }
    return $article_data;
}
function getArticleContent($article_path) {
    $article = file_get_contents($article_path);
    preg_match_all('/<\/(.*?)>/s', $article, $tags);
    $content = "";
    foreach ($tags[1] as $tag) {
        if ($tag == 'header_image') {
            $article_data = getArticleInlineData($article, $tag);
            $content .= "<div class=\"article-loaded-header-image\" id=\"".$article_path."-loaded\">$article_data</div>";
        } elseif ($tag == 'headline') {
            continue;
        } else {
            $article_data = getArticleInlineData($article, $tag);
            $content .= "<div class=\"article-loaded\" id=\"".$article_path."-loaded\">$article_data</div>";
        }
    }
    $content .= "<br><br><br><br><br><br><br>";
    return $content;
}
function getArchive($article_array) {
    global $lang;
    $article_array = getArticleArray($lang);
    $per_page = 20;
    $num_of_pages = count($article_array) / $per_page;;
    global $page;

    $content = "";
    foreach (paginateArray($article_array, $page * $per_page, $per_page) as $article_path) {
        $content .= "
            <div class=\"archive\" id=".$article_path." onclick=\"displayArticle(this.id)\">
                <div class=\"archive-title\" style=\"text-align:left;\">
                    ".getArticleData($article_path, 'title', 0)."
                </div>

                <div class=\"archive-time-stamp\" style=\"text-align:left;\">
                    ".getArticleData($article_path, 'time_stamp', 0)."
                </div>
            </div>
        ";
    }
    $content .= printPagination($lang, $page, $num_of_pages);
    return $content;
}
function getSearch($article_array, $search_string) {
    // $search_string = str_replace("_", " ", $search_string);
    $search_results = array();
    // TODO improve search engine to exlude <article_tags>
    // TODO improve search engine by splitting search string into items, and checking for each item (e.g. "search these items" => "search", "these", "items")
    // TODO make search results paginateable
    foreach ($article_array as $article_path) {
        // $title = getArticleData($article_path, 'title', 0);
        // $tags = getArticleData($article_path, 'tags', 1);
        // $content = getArticleData($article_path, 'content', 0);
        // if (stripos($title, $search_string)) {
        //     array_push($search_results, $article_path);
        // } else if (stripos($tags, $search_string)) {
        //     array_push($search_results, $article_path);
        // } else if (stripos($content, $search_string)) {
        //     array_push($search_results, $article_path);
        // }
        $article = file_get_contents($article_path);
        if (stripos($article, $search_string)) {
            array_push($search_results, $article_path);
        }
    }
    return $search_results;
}
function getTag($article_array, $search_string) {
    $search_string = str_replace("_", " ", $search_string);
    $search_results = array();
    foreach ($article_array as $article_path) {
        $article_tag = 'tags';
        $article = file_get_contents($article_path);
        $article_data = substr($article, strpos($article, '<'.$article_tag.'>') + strlen('<'.$article_tag)); // TODO missing .'>' bug? won't load first tag otherwise
        $article_data = substr($article_data, 0, strpos($article_data, '</'.$article_tag.'>'));
        if (strpos($article_data, $search_string)) {
            array_push($search_results, $article_path);
        }
    }
    return $search_results;
}
function paginateArray($array, $page, $per_page) {
    $i = 0;
    foreach ($array as $key => $value) { if ($page <= $key && $i < $per_page) { $i++; yield $key => $value; } }
}
function printPagination($lang, $page, $num_of_pages) {
    $content = "";
    $content .= "<div class=\"pagination\">";
    if ($page >= 1) { $content .= "<a href=?lang=".$lang."&page=".($page - 1)."> < </a>  &nbsp;&nbsp;  "; } else { $content .= "<a style=\"opacity:0.3\"><</a> &nbsp;&nbsp; "; }
        for ($i = 0; $i <= floor(($num_of_pages)); $i++) { if ($page !== $i) { $content .= "<a href=?lang=".$lang."&page=$i>".($i + 1)."</a>  &nbsp;&nbsp;  ";
        } else { $content .= "<a style=\"opacity:0.3\">".($i + 1)."</a>  &nbsp;&nbsp;  "; } }
    if ($page <= ($num_of_pages - 1)) { $content .= "<a href=?lang=".$lang."&page=".($page + 1)."> > </a>"; } else { $content .= "<a style=\"opacity:0.3\">></a>"; }
    $content .= "</div>";
    return $content;
}
function printHeader($lang) {
    $lang_options = array('en', 'fr', 'ru');
    // $home = $_SERVER['SERVER_NAME'].'/index.php';
    $home = '/newcorp/index.php';
    $content = "
        <div class=\"header\">

            <a href=".$home." />
            <div class=\"logo\">
                <img src=\"css/a.jpg\">
            </div>
            </a>

            <div class=\"language\">
                <div class=\"language-button\"><img src=\"css/globe.png\"></div>
                <div class=\"language-dropdown\">";

    foreach ($lang_options as $lang_option) {
        if ($lang_option.'/' == $lang) {
            $content .= "
                <div class=\"language-option\"><a style=\"opacity: 0.5;\"><img src=\"css\/".$lang_option.".png\"></a></div>";
        } else {
            $content .= "
                <div class=\"language-option\"><a href=?lang=".$lang_option."/><img src=\"css\/".$lang_option.".png\"></a></div>";
        }
    }
    $content .= "
                </div>

            </div>
        </div>
        <div class=\"block\">
        </div>
    ";
    return $content;
}
function printSearch() {
    // <div class=\"search-glass\" style=\"position:absolute;font-size:40px;top:5%;\">&#128269;</div> // temp old search icon?
    $content = "
        <div class=\"search-button\" onclick=\"openSearch()\">
            <img src=\"css/search.png\">
        </div>

        <div id=\"search-view\" class=\"search-view\">
        </div>

        <div id=\"search-bar\" class=\"search-bar\">
            <div id=\"close-search\" class=\"close-search\" onclick=\"closeSearch()\">&times;</div>
            <form action=\"\">
                <input id=\"search\" type=\"text\" name=\"search\" placeholder=\"Search...\"
                style=\"
                    color: white;
                    font-size:60px;
                    margin:auto;
                    width:100%;
                    text-align:center;
                    -webkit-border-radius: 10px;
                    -moz-border-radius: 10px;
                    border-radius: 10px;
                    \">
                <input type=\"submit\" style=\"position: absolute; left: -9999px; width: 1px; height: 1px;z-index:-99;\" tabindex=\"-1\" />
            </form>
        </div>
    ";
    return $content;
}
function printNavigation() {
    $content = "
        <div class=\"navigation\">

                <a href=\"index.php\">HOME</a>&nbsp;&nbsp;
                <a href=\"info.php\">INFO</a>&nbsp;&nbsp;
                <a href=\"archive.php\">ARCHIVE</a>

        </div>
    ";
    return $content;
}
function printAdvert() {
    $content = "
        <div class=\"advert\">
            advert
        </div>
    ";
    return $content;
}
function printFeatures() {
    $features = scandir('features/');
    // ARRAY OF ARTICLES -- make array of text docs
    $features_array = array();
    foreach ($features as $feature) {
        if (strpos($feature, '.txt')) {
            array_push($features_array, 'features/'.$feature);
        }
    }
    $feature_1_path = $features_array[0];
    $feature_2_path = $features_array[1];
    $feature_3_path = $features_array[2];

    $content = "
        <div class=\"feature\">
            <div class=\"highlight-1\" id=".$feature_1_path." onclick=\"displayArticle(this.id)\">
                ".getArticleData($feature_1_path, 'header_image', 0)."
                <div class=\"highlight-1-title\">
                    <p style=\"margin:5px;text-transform:uppercase;font-weight:bold;\">".getArticleData($feature_1_path, 'title', 0)."</p>
                </div>
                <div class=\"highlight-1-content\">
                    <p style=\"margin:5px;color:rgba(0, 0, 0, 0.5);\">".getArticleData($feature_1_path, 'time_stamp', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_1_path, 'tags', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_1_path, 'headline', 0)."</p>
                    <div class=\"article-runoff\"><p style=\"margin:5px;\">".getArticleData($feature_1_path, 'content', 0)."</p></div>
                </div>
            </div>

            <div class=\"highlight-2\"  id=".$feature_2_path." onclick=\"displayArticle(this.id)\">
                ".getArticleData($feature_2_path, 'header_image', 0)."
                <div class=\"highlight-2-title\">
                    <p style=\"margin:5px;text-transform:uppercase;font-weight:bold;\">".getArticleData($feature_2_path, 'title', 0)."</p>
                </div>
                <div class=\"highlight-2-content\">
                    <p style=\"margin:5px;color:rgba(0, 0, 0, 0.5);\">".getArticleData($feature_2_path, 'time_stamp', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_2_path, 'tags', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_2_path, 'headline', 0)."</p>
                    <div class=\"article-runoff\"><p style=\"margin:5px;\">".getArticleData($feature_2_path, 'content', 0)."</p></div>
                </div>
            </div>

            <div class=\"highlight-3\"  id=".$feature_3_path." onclick=\"displayArticle(this.id)\">
                ".getArticleData($feature_3_path, 'header_image', 0)."
                <div class=\"highlight-3-title\">
                    <p style=\"margin:5px;text-transform:uppercase;font-weight:bold;\">".getArticleData($feature_3_path, 'title', 0)."</p>
                </div>
                <div class=\"highlight-3-content\">
                    <p style=\"margin:5px;color:rgba(0, 0, 0, 0.5);\">".getArticleData($feature_3_path, 'time_stamp', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_3_path, 'tags', 0)."</p>
                    <p style=\"margin:5px;\">".getArticleData($feature_3_path, 'headline', 0)."</p>
                    <div class=\"article-runoff\"><p style=\"margin:5px;\">".getArticleData($feature_3_path, 'content', 0)."</p></div>
                </div>
            </div>
        </div>
    ";
    return $content;
}
function printArticle($article_path) {
    $content = "
        <div class=\"article\" id=".$article_path." onclick=\"displayArticle(this.id)\">
            ".getArticleData($article_path, 'header_image', 0)."
            <div class=\"article-title\">
                <p style=\"margin:5px;text-transform:uppercase;font-weight:bold;\">".getArticleData($article_path, 'title', 0)."</p>
            </div>
            <div class=\"article-content\">
                <p style=\"margin:5px;color:rgba(0, 0, 0, 0.5);\">".getArticleData($article_path, 'time_stamp', 0)."</p>
                <p style=\"margin:5px;\">".getArticleData($article_path, 'tags', 0)."</p>
                <p style=\"margin:5px;\">".getArticleData($article_path, 'headline', 0)."</p>
                <div class=\"article-runoff\"><p style=\"margin:5px;\">".getArticleData($article_path, 'content', 0)."</p></div>
            </div>
        </div>
    ";
    return $content;
}
function printInfo() {
    $content = "
        <div class=\"bio-info\">
            <p>Lorem ipsum dolor.</p>
        </div>
    ";
    return $content;
}
function printContactForm() {
    // <input type=\"button\" value=\"SEND\" onclick=\"sendMail()\" style=\"cursor:pointer;\"> // temp old send button?
    $content = "
        <div class=\"contact-form\">
            <form action=\"\" method=\"post\" style=\"text-align:center;\">
                <input id=\"email\" name=\"email\" type=\"text\" style=\"width:100%;text-align:center;\" placeholder=\"Enter E-mail Here\"><br>
                <br><textarea id=\"message\" name=\"message\" rows=\"13\" style=\"width:100%\" placeholder=\"Enter Message Here\"></textarea><br>
                <input type=\"text\" id=\"name\" name=\"name\" style=\"display:none;\"/>
                <br><img src=\"css/send.png\" onclick=\"sendMail()\" style=\"cursor:pointer;width: 40px; height: 40px;margin-bottom: 7vh;\">
            </form>
        </div>
    ";
    return $content;
}
function printFooter() {
    $content = "
        <div class=\"footer\" id=\"footer\"><br><br>
            <p>Lorem ipsum dolor sit amet</p>
            <p>consectetur adipiscing elit.</p>
            <p>Etiam non mattis metus. Sed eget</p>
            <p>mauris sed magna pharetra imperdiet.</p>
            <div class=\"social-media\">
                <a href=\"\"><img src=\"css/facebook.png\" style=\"height: 40px;width: 40px;padding-left:30px;padding-right:30px;\"></a>
                <a href=\"\"><img src=\"css/twitter.png\" style=\"height: 40px;width: 40px;padding-left:30px;padding-right:30px;\"></a>
                <a href=\"\"><img src=\"css/instagram.png\" style=\"height: 40px;width: 40px;padding-left:30px;padding-right:30px;\"></a>
            </div>
            <p>LOGOS</p>
        <br><br></div>
    ";
    return $content;
}
function displayArticleDrawer() {
    $content = "
        <div id=\"open-article\" class=\"open-article\">
            <a href=\"javascript:void(0)\" class=\"close-article\" id=\"close-article\" onclick=\"closeArticle()\">&times;</a>
            <div class=\"display-article-content\"></div>
        </div>
    ";
    return $content;
}

?>
