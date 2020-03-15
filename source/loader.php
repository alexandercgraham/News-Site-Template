<?php
function getArticleData($article, $article_tag) {
    $article_data = substr($article, strpos($article, '<'.$article_tag.'>') + strlen('<'.$article_tag.'>'));
    $article_data = substr($article_data, 0, strpos($article_data, '</'.$article_tag.'>'));
    if (!$article_data) {
        $article_data = "404: ".$article_tag."!";
    }
    return $article_data;
}
function printArticleContent($article_path) {
    $article = file_get_contents($article_path);
    preg_match_all('/<\/(.*?)>/s', $article, $article_tags);
    $content = "";
    foreach ($article_tags[1] as $article_tag) {
        if ($article_tag == 'header_image') {
            $article_data = getArticleData($article, $article_tag);
            $content .= "<div class=\"article-loaded-header-image\" id=\"".$article_path."-loaded\">$article_data</div>";
        } elseif ($article_tag == "tags") {
            $article_data = getArticleData($article, $article_tag);
            $tags = explode(",", $article_data);
            $article_data = "<div class=\"article-loaded\">";
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $article_data .= "
                    <a style=\"color:white;background: rgba(0, 0, 0, 0.1);border-radius:3px;\" href=\"https://".$_SERVER['HTTP_HOST']."/newcorp/?tag=".str_replace(" ", "_", $tag)."\">
                    &nbsp;".$tag."&nbsp;</a>&nbsp;&nbsp;
                "; // TODO make dynamic
            }
            $content .= $article_data."</div>";
        } elseif ($article_tag == 'headline') {
            continue;
        } else {
            $article_data = getArticleData($article, $article_tag);
            $content .= "<div class=\"article-loaded\" id=\"".$article_path."-loaded\">$article_data</div>";
        }
    }
    $content .= "
        <br>
        <div class=\"advert\">
            advert
        </div>
        <br><br>
    ";
    // $content .= "<br><br><br><br>";
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

if (isset($_REQUEST['article_path'])) {
    $article_path = $_REQUEST['article_path'];

    $content = printArticleContent($article_path);
    // $content .= printAdvert();

    echo $content;
    exit();
}
if (isset($_REQUEST['send_email'])) {
    if ($_REQUEST['name']){
        exit();
    }
    if ($_COOKIE['mail']) {
        echo "wait 10 minutes before sending another email";
        exit();
    }

    $email = htmlspecialchars($_REQUEST['email']);

    $to = "email@domain.com";
    $subject = "Contact Form Mail";
    $message = $email."\n\n".htmlspecialchars($_REQUEST['message']);
    $header = "Contact: ".$email;
    // mail($to, $subject, $message, $header);

    $from = $email;
    $subject_2 = "Copy Mail";
    $message_2 = "Copy of your mail submission: ".$from."\n\n".htmlspecialchars($_REQUEST['message']);
    $header_2 = "Copy Contact: ".$to;
    // mail($from, $subject_2, $message_2, $header_2);

    if (mail($from, $subject_2, $message_2, $header_2)) {
        setcookie("mail", 1, time() + (10 * 60), '/');
        mail($to, $subject, $message, $header);
        echo "success";
    } else {
        echo "failure";
    }

    // echo "Your message has been received. We will contact you soon.";
    exit();
}

?>
<script>
</script>
