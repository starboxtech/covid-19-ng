<footer>
    <div class="container boxed">
        <hr>
        <div class="footer-top">
            <p>Contact us at hello@starboxtech.com</p>
            <div class="social footer-aside">
                <div>Follow us @starboxtech</div>
                <div class="social-icons">
                    <a href="https://www.instagram.com/starboxtech/">
                        <?php include(IMG_PATH . '/instagram.svg') ?>
                    </a>
                    <a href="https://twitter.com/starboxtech/">
                        <?php include(IMG_PATH . '/twitter.svg') ?>
                    </a>
                    <a href="https://www.facebook.com/starboxtech/">
                        <?php include(IMG_PATH . '/facebook.svg') ?>
                    </a>
                    <a href="https://www.linkedin.com/company/starboxtech/">
                        <?php include(IMG_PATH . '/linkedin.svg') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bar bg-grey">
        <div class="container boxed">
            <div class="footer-bottom footer-aside">
                <span>&copy <?= date('Y') ?> <img src="<?= IMG_ROOT . '/favicon.png' ?>" alt="StarBox Logo"
                        height="18px" width="auto"> <a class="text-reset" href="<?= STARBOX ?>"><u>Starbox Technologies
                            Ltd.</u></a></span>
                <span class="footer-heart">
                    <?php include(IMG_PATH . '/heart.svg') ?>
                    <?php include(IMG_PATH . '/ng.svg') ?>
                </span>
            </div>
        </div>
    </div>
</footer>