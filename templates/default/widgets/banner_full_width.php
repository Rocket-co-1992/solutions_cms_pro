<?php

debug_backtrace() || die ("Direct access not permitted");

$img_path = $widget->getMainFile();

if(!empty($img_path)){ ?>

	<section class="banner-wrap mt40 mb40 pt40 pb40" data-bg="<?php echo $img_path; ?>">
		<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-md-8 text-center text-white">
						<?php echo $widget->content; ?>
					</div>
				</div>
			</div>
	</section>
	
	<?php
}