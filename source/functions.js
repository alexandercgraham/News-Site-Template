jQuery(function() {
    var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    jQuery("html, body").css({"width":w,"height":h});
});
window.onresize = function (event) {
    var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    jQuery("html, body").css({"width":w, "height":h});
    // alert(h);
    // jQuery(function() {
    //     var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    //     var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    //     jQuery("html, body").css({"width":w,"height":h});
    // });
}

function displayArticle(article_path) {
    if (jQuery(event.target).closest("#tag-links").length){
        // alert("break");
    } else {
        // alert("load");
        jQuery("div.open-article").animate({ scrollTop: 0 }, "fast");
        jQuery("#loading_image").css({ "display" : "block" });
        jQuery.ajax({
            type: "POST",
            url: "loader.php",
            data: {article_path: article_path},
            success: function(data) {
                // console.log(data);
                jQuery("div.display-article-content").html(data);
                openArticle();
                jQuery("#loading_image").css({ "display" : "none" });
            }
        });
    }
    // TODO if click is on tag href / class or id tag-links, don't load article
    // jQuery("div.open-article").animate({ scrollTop: 0 }, "fast");
    // jQuery("#loading_image").css({ "display" : "block" });
    // jQuery.ajax({
    //     type: "POST",
    //     url: "loader.php",
    //     data: {article_path: article_path},
    //     success: function(data) {
    //         // console.log(data);
    //         jQuery("div.display-article-content").html(data);
    //         openArticle();
    //         jQuery("#loading_image").css({ "display" : "none" });
    //     }
    // });
}
jQuery(document).click(function(event) {
    $target = jQuery(event.target);
    if (document.getElementById("search-view").style.display == "block") { //if div exists
        if (!$target.closest("#search-bar").length) {
            closeSearch();
        }
    }
});
function openSearch() {
    closeArticle();

    jQuery.ajax({
        success: function(data) {
            // console.log(data);
            // document.getElementById("close-search").style.display = "block"; // temp removal
            document.getElementById("search-view").style.display = "block";
            document.getElementById("search-bar").style.display = "block";
        }
    });
}
function closeSearch() {
    // document.getElementById("close-search").style.display = "none"; // temp removal
    document.getElementById("search-view").style.display = "none";
    document.getElementById("search-bar").style.display = "none";
}
jQuery(document).click(function(event) {
    $target = jQuery(event.target);
    if (document.getElementById("open-article")) { //if div exists
        if (!$target.closest("#open-article").length) {
            document.getElementById("footer").style.backgroundColor = "black";
            document.getElementById("close-article").style.display = "none";
            document.getElementById("open-article").style.width = "0";
            document.body.style.overflow = "auto";
        }
    }
});
function openArticle() {
    // document.getElementById("footer").style.backgroundColor = "white";
    document.getElementById("close-article").style.display = "block";
    document.getElementById("open-article").style.width = "100%";
    document.body.style.overflow = "hidden";
}
function closeArticle() {
    // document.getElementById("footer").style.backgroundColor = "black";
    document.getElementById("close-article").style.display = "none";
    document.getElementById("open-article").style.width = "0";
    document.body.style.overflow = "auto";
}
function sendMail() {
    var send_email = true;
    var email = document.getElementById('email').value;
    email = email.trim();
    var message = document.getElementById('message').value;
    var name = document.getElementById('name').value;
    if (!email || !message) {
        alert("Please fill in all fields.");
        return;
    }
    if (!validateEmail(email)) {
        alert("Invalid email.");
        return;
    }
    if (email && message && !name) {
        jQuery.ajax({
            type: "POST",
            url: "loader.php",
            data: {send_email: send_email, name: name, email: email, message: message, name: name},
            success: function(data){
                alert(data);
            }
        });
    }
}
function validateEmail(emailAddress) {
    var sQtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
    var sDtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
    var sAtom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
    var sQuotedPair = '\\x5c[\\x00-\\x7f]';
    var sDomainLiteral = '\\x5b(' + sDtext + '|' + sQuotedPair + ')*\\x5d';
    var sQuotedString = '\\x22(' + sQtext + '|' + sQuotedPair + ')*\\x22';
    var sDomain_ref = sAtom;
    var sSubDomain = '(' + sDomain_ref + '|' + sDomainLiteral + ')';
    var sWord = '(' + sAtom + '|' + sQuotedString + ')';
    var sDomain = sSubDomain + '(\\x2e' + sSubDomain + ')*';
    var sLocalPart = sWord + '(\\x2e' + sWord + ')*';
    var sAddrSpec = sLocalPart + '\\x40' + sDomain; // complete RFC822 email address spec
    var sValidEmail = '^' + sAddrSpec + '$'; // as whole string

    var reValidEmail = new RegExp(sValidEmail);

    return reValidEmail.test(emailAddress);
}
