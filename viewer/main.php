<?php
/*
 * EasyGallery
 * http://github.com/romaricdrigon/
 */

/* Config */
// do you want to use thumbnails? 60px images, galleries will load faster (big pictures are not loaded at the same time than the page)
// it'll degrade smartly if no thumb is found - but disabling it may allow you to save a little time if you don't have any thumb
$use_thumbs = TRUE;
$thumbs_suffix = '_thumb'; // the thumbs corresponding to 'IMG42.jpg' should be called 'IMG42_thumb.jpg'
$lang = 'es'; // either fr or en
/* end of config, no more things to modify */

require_once 'lister.php';
require_once 'resources/lang/' . $lang . '.php';

$subdir = get_folder(); // get only subdirectory
$dir = ($subdir == '') ? 'resources/photos' : 'resources/photos/' . $subdir; // full path - check if there's subdir to avoid ending /

// scan folder
$list = lister($dir, $use_thumbs, $thumbs_suffix);
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $messages['title']; ?></title>
	<link type="text/css" href="./viewer/style.css" rel="stylesheet"/>
	<!-- jQuery -->
	<script type="text/javascript" src="./viewer/lib/jquery.min.js"></script>
	<!-- Galleria (jQuery plugin) -->
	<script type="text/javascript" src="./viewer/lib/galleria/galleria-1.3.2.min.js"></script>
	<script type="text/javascript" src="./viewer/lib/galleria/themes/classic/galleria.classic.min.js"></script>
	<script type="text/javascript" src="./viewer/lib/galleria/plugins/history/galleria.history.min.js"></script>
	<link rel="stylesheet" href="./viewer/lib/galleria/themes/classic/galleria.classic.css">
	<link rel="shortcut icon" type="image/png" href="./resources/favicon.png"/>
</head>
<body>
<div class="main" id="main">
	<div class="header"><h2><?php echo $messages['title']; ?></h2></div>
	<!-- <div class="path"><?php show_path($subdir); ?></div> -->
	<div class="gallery" id="gallery">
		<!-- link to images - will be displayed if Javascript is disabled -->
        <?php
        // show link to images
        foreach ($list['picture'] as $pic) {
            if (isset($pic['big']) && ($pic['big'] !== 'thumbnail.jpg')) { // we check if the big picture exists
                if (($use_thumbs === TRUE) && (isset($pic['thumb']))) {
                    echo '<a href="' . link_safe($dir . '/' . $pic['big']) . '"><img src="' . link_safe($dir . '/' . $pic['thumb']) . '" /></a><br />' . "\n";
                } else {
                    // if no thumb is provided, Galleria will use the big picture automatically
                    echo '<img src="' . link_safe($dir . '/' . $pic['big']) . '" /><br />' . "\n";
                }
            }
        }
        ?>
	</div>
    <?php
    if (isset($list['folder']) && sizeof($list['folder']) !== 0):
        ?>
		<div class="gallery_list">
			<h3><?php echo $messages['subgalleries']; ?></h3>
            <?php
            // show link to images
            foreach ($list['folder'] as $fol) {
                // test to avoid one more slash
                $path = ($subdir == '') ? $fol : $subdir . '/' . $fol;

                echo '<a href="?gallery=' . gallery_link($path) . '" title="' . link_safe($fol) . '">' .
                    '<div class="thumb" style="background-image: url(&quot;' . link_safe($dir . '/' . $fol . '/' . get_thumbnail($dir . '/' . $fol)) . '&quot;);">' .
                    '<div class="text">' . html_safe($fol) . '</div></div></a>' . "\n";
            }
            ?>
			<br clear="all"/>
		</div>
    <?php
    endif;
    ?>
	<div class="footer">
        <?php
        // display the comment only if there's a gallery
        if (isset($list['picture']) && sizeof($list['picture']) !== 0):
            ?>
			<span class="white"><?php echo $messages['keyboards_shortcuts']; ?></span>
        <?php
        endif;
        ?>
		<p class="grey"><?php echo $messages['copyright']; ?></p>
	</div>
</div> <!-- end main -->
<!-- finally, we load galleria -->
<script>
    // no need to loadTheme, already included
    Galleria.run('#gallery');

    // Galleria options, you may want to take a look
    // doc cf. http://galleria.io/docs/options/
    Galleria.configure({
        carousel: true, // the carousel with thumbnails
        debug: false, // will display error messages
        height: 700,
        width: 960,
        imageCrop: false, // scale down the image, no cropping
        showCounter: true, // the images counter
        showImagenav: true, // navigation arrows
        // swipe: true provokes issues with Galleria 1.3.0+
        thumbCrop: true, // same that imageCrop - "fill the square"
        transition: 'slide' // between images
    });

    // bind keyboard shortcuts when Galleria is ready
    Galleria.ready(function () {
        // bind function to keyboard using Galleria API
        this.attachKeyboard({
            left: this.prev,
            right: this.next,
            13: function () {
                this.toggleFullscreen(); // enter will launch/quit full screen mode
            },
            32: function () {
                this.playToggle(); // play or pause slideshow when space is pressed
            },
            27: this.pause // Esc can exit full screen, but also stop slideshow
        });
    });
</script>
</body>
</html>
