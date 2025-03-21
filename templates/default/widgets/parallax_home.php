<?php

debug_backtrace() || die ("Direct access not permitted");

$img_path = $widget->getMainFile();

if(!empty($img_path)){ ?>

	<section class="parallax-wrap mt-75" snake-parallax="hero" data-background="<?php echo $img_path; ?>">
		<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-8 text-center">
						<?php echo $widget->content; ?>
					</div>
				</div>
			</div>
	</section>
	
	<?php
}