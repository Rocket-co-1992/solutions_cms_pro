<?php

use Pandao\Common\Utils\DateUtils;

debug_backtrace() || die ('Direct access not permitted');

$siteContext = Pandao\Services\SiteContext::get();
$myPage = $siteContext->currentPage;

$months = $siteContext->getArticlesCountByMonth();

$articles = $siteContext->getArticles([
	'id_page' => $myPage->id,
	'limit' => 5,
	'excluded_ids' => [$siteContext->currentArticle->id ?? null],
]);

if(!empty($myPage->articles)){ ?>

	<div class="single-sidebar-widget">
		<h3 class="wid-title"><?php echo $siteContext->texts['RECENT_ARTICLES']; ?></h3>
		<div class="popular-posts">

			<?php
			foreach($articles as $article){ ?>

				<div class="single-post-item">
					<div class="thumb bg-cover" style="background-image: url('<?php echo $article->getMainImagePath('small'); ?>')"></div>
					<div class="post-content">
						<h5><a href="<?php echo $article->path; ?>"><?php echo $article->title; ?></a></h5>
						<div class="post-date">
							<i class="fa-regular fa-calendar-alt"></i><?php echo DateUtils::strftime(PMS_DATE_FORMAT, $article->publish_date); ?>
						</div>
					</div>
				</div>

				<?php
			} ?>

		</div>
	</div>
	
	<div class="single-sidebar-widget">
		<h3 class="wid-title"><?php echo $siteContext->texts['ARCHIVES']; ?></h3>

		<ul>
			<?php
			foreach ($months as $d => $count) { ?>
				<li><a href="<?php echo $myPage->path . '?month=' . date('n', $d) . '&year=' . date('Y', $d); ?>"><?php echo DateUtils::strftime('%B %Y', $d).' ('.$count.')'; ?></a></li>
				<?php
			} ?>
		</ul>

	</div>
	<?php
} 

if(!empty($myPage->tags)) { ?>

	<div class="single-sidebar-widget">
		<h3 class="wid-title"><?php echo $siteContext->texts['TAGS']; ?></h3>

		<?php
		foreach($myPage->tags as $tag){ ?>
			<a href="<?php echo $myPage->alias . '?tag=' . $tag['id']; ?>" class="btn btn-secondary mb5"><?php echo $tag['value']; ?></a>
			<?php
		} ?>

	</div>

	<?php
}