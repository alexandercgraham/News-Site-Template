# News-Site-Template
Template for a media-driven news / blog website with multi-language support, e-mail contact form, and asynchronous article loading. Built with PHP, JS, and JQuery AJAX.

Live example powered at : zanderfleurich.com/news

Suitable for hobbyists / non-power users.

# Adding Articles
Articles are stored in plain ".txt" files in language directories (e.g. "en", "fr", "ru"). Articles are built by surrounding blocks of data with \<brackets\>, such as \<header_image\>, \<author\>, \<title\>, \<content\>, etc. Article images and other media are added in-line with \<img src=\"\"\> references. Media referenced should be stored in the "media" directory.

The 3 featured articles on the main page are located in the "features" directory and follow the same ".txt" format. Featured articles can be disabled by removing the call to the "printFeatures()" function under "index.php".

# Language Support
Language support can be added / removed by editing the language abbreviation (e.g. "sp" for Spanish) in the "$lang_options" array under the "printHeader()" function in the "functions.php" file. A directory named after the abbreviation must then be created where corresponding language ".txt" files will be stored. Default language can be changed by editing "index.php" where the language COOKIE value is requested and assigned to the "$lang" variable.

# Contact Form
The default e-mail in which contact correspondence is sent to can be changed under "loader.php" at the "$to" variable.

E-mails are validated by the "validateEmail()" function under "functions.js".

# Page Content
Page content on the "Home", "Info", and "Archive" pages can be found within functions under "functions.php", where content is pre-created in blocks to be referenced by function calls.

Button and logo images can be found under the "css" directory along with the "main.css" file where most styling can be found.
