<?php
declare(strict_types=1);

// Page bottom: closes <main> and adds the site footer.
// Written with help from Claude (AI). See README, "AI use".
//
// Each page sets $footer_label before including this file.

?>
</main>

<footer class="site-footer">
  <span><?= e($footer_label) ?></span>
  <span>Drexel University</span>
</footer>

</body>
</html>
