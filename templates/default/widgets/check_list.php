<?php

debug_backtrace() || die ("Direct access not permitted");

if(!empty($widget->content)){
	$text_widget = explode('<hr>',$widget->content);
	
	if(count($text_widget) > 0){ ?>

		<div class="container mt20 mb40">
			<div class="row">
				<?php
				foreach($text_widget as $content){ ?>

					<div class="col-lg-3 text-center mb-3">
						<article class="single-service-card iconBlocHome">
							<?php echo $content; ?>
						</article>
					</div>

					<?php
				} ?>
			</div>
		</div>

		<?php
	}
} ?>
