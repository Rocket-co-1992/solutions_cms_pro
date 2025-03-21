<?php

namespace Pandao\Services;

use Pandao\Models\Article;
use \PDO;

class ArticleService
{
    protected $pms_db;
    protected $allArticles;
    protected $siteContext;

    public function __construct($db, $siteContext)
    {
        $this->pms_db = $db;
        $this->siteContext = $siteContext;
    }

    /**
     * Retrieves all articles associated with the given pages.
     *
     * @param array $pageIds Array of page IDs.
     * @param array $allPages Array of all Page objects for path resolution.
     * @return Article[] Array of Article objects.
     */
    public function getAllArticles(array $pageIds, array $allPages)
    {
        if (empty($pageIds)) {
            return [];
        }

        $articles = [];

        $stmt = $this->pms_db->query('SELECT * FROM solutionsCMS_article
                                    WHERE id_page IN(' . implode(',', $pageIds) . ')
                                    AND (checked = 1 OR checked = 3)
                                    AND (publish_date IS NULL OR publish_date <= ' . time() . ')
                                    AND (unpublish_date IS NULL OR unpublish_date > ' . time() . ')
                                    AND lang = ' . PMS_LANG_ID . '
                                    AND (show_langs IS NULL OR show_langs = \'\' OR show_langs REGEXP \'(^|,)' . PMS_LANG_ID . '(,|$)\')
                                    AND (hide_langs IS NULL OR hide_langs = \'\' OR hide_langs NOT REGEXP \'(^|,)' . PMS_LANG_ID . '(,|$)\')
                                    ORDER BY CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END DESC');

        if ($stmt !== false) {

            $articleFileStmt = $this->pms_db->prepare('SELECT *
                                                    FROM solutionsCMS_article_file
                                                    WHERE id_item = :article_id
                                                        AND checked = 1 AND lang = :lang
                                                        AND `type` = \'image\' 
                                                        AND file != \'\' 
                                                    ORDER BY `rank`');
        
            $articleCommentStmt = $this->pms_db->prepare('SELECT * 
                                                    FROM solutionsCMS_comment 
                                                    WHERE id_item = :article_id 
                                                        AND item_type = \'article\' 
                                                        AND checked = 1
                                                    ORDER BY add_date DESC');

            $articleTagsStmt = $this->pms_db->prepare('SELECT * 
                                                    FROM solutionsCMS_tag
                                                    WHERE FIND_IN_SET(id, :article_tags)
                                                        AND lang = :lang
                                                    ORDER BY `rank`');
            
            foreach ($stmt as $row) {

                $articleFileStmt->execute(['article_id' => $row['id'], 'lang' => PMS_LANG_ID]);
                $row['images'] = $articleFileStmt->fetchAll(PDO::FETCH_ASSOC);

                $articleTagsStmt->execute(['article_tags' => $row['tags'], 'lang' => PMS_LANG_ID]);
                $row['tags_values'] = $articleTagsStmt->fetchAll(PDO::FETCH_ASSOC);

                $articleCommentStmt->execute(['article_id' => $row['id']]);
                $itemComments = $articleCommentStmt->fetchAll(PDO::FETCH_ASSOC);
        
                $row['num_comments'] = count($itemComments);
                $row['comments'] = $itemComments;
        
                $article = new Article($this->pms_db, $this->siteContext);
                $article->populateProperties($row);
        
                $article->path = empty($article->url) ? $allPages[$article->id_page]->path . '/' . $article->alias : $article->url;
                
                $article->images = $row['images'];

                $article->itemComments = $row['comments'];
                $article->num_comments = $row['num_comments'];

                $article->tags_values = $row['tags_values'];
        
                $articles[$row['id']] = $article;
            }
        }
        $this->allArticles = $articles;

        return $articles;
    }

    /**
     * Retrieves an article by ID.
     *
     * @param int $id The article ID.
     * 
     * @return Article|null The article object or null if not found.
     */
    public function getArticle($id)
    {
        foreach ($this->allArticles as $article) {
            if ($article->id === $id)
                return $article;
        }
        return null;
    }

    /**
     * Retrieves an article by its alias.
     *
     * @param string $alias The article alias.
     * 
     * @return Article|null The article object or null if not found.
     */
    public function getArticleByAlias($alias)
    {
        foreach ($this->allArticles as $article) {
            if ($article->alias === $alias) {
                return $article;
            }
        }
        return null;
    }

    /**
     * Retrieves articles based on the page ID.
     *
     * @param int $id_page The page ID.
     * 
     * @return array The array of articles associated with the page.
     */
    public function getArticlesByPageId($id_page)
    {
        $filtered_articles = [];

        foreach ($this->allArticles as $id_article => $article) {
            if ($article->id_page == $id_page) {
                $filtered_articles[$id_article] = $article;
            }
        }
        return $filtered_articles;
    }

    /**
     * Retrieves filtered articles based on the specified options.
     *
     * @param array $options Options for filtering the articles.
     * @return array The array of filtered articles.
     */
    public function getFilteredArticles($options = [])
    {
        $allArticles = $this->allArticles;
        $defaults = [
            'id_page' => null,
            'home' => null,
            'excluded_ids' => [],
            'checked' => 1,
            'lang' => PMS_LANG_ID,
            'limit' => null,
            'offset' => 0,
            'current_time' => time(),
            'start_date' => null,
            'end_date' => null,
            'sort' => 'rank',
            'tag' => null
        ];

        $options = array_merge($defaults, $options);

        $filtered_articles = array_filter($allArticles, function($article) use ($options) {
            if (in_array($article->id, $options['excluded_ids'])) return false;
            if ($article->checked != $options['checked']) return false;
            if ($article->lang != $options['lang']) return false;
            if (!is_null($article->publish_date) && $article->publish_date > $options['current_time']) return false;
            if (!is_null($article->unpublish_date) && $article->unpublish_date <= $options['current_time']) return false;
            if ((!is_null($options['id_page']) && $article->id_page != $options['id_page']) || (is_null($options['id_page']) && $article->home != $options['home'])) return false;
            if (!empty($article->show_langs) && !preg_match('/(^|,)' . $options['lang'] . '(,|$)/', $article->show_langs)) return false;
            if (!empty($article->hide_langs) && preg_match('/(^|,)' . $options['lang'] . '(,|$)/', $article->hide_langs)) return false;

            if ($options['start_date'] || $options['end_date']) {
                $start_date = strtotime($options['start_date']);
                $end_date = strtotime($options['end_date']);

                $date_to_check = !is_null($article->publish_date) ? strtotime($article->publish_date) : strtotime($article->add_date);

                if ($start_date && $date_to_check < $start_date) return false;
                if ($end_date && $date_to_check > $end_date) return false;
            }

            if (!is_null($options['tag']) && !empty($article->tags)) {
                if (!preg_match('/(^|,)' . $options['tag'] . '(,|$)/', $article->tags)) {
                    return false;
                }
            }

            return true;
        });

        if ($options['sort'] == 'rank') {
            usort($filtered_articles, function($a, $b) {
                return $a->rank <=> $b->rank;
            });
        }
        if ($options['sort'] == 'latest') {
            usort($filtered_articles, function($a, $b) {
                return $a->add_date <=> $b->add_date;
            });
        }

        if ($options['limit'] || $options['offset'] >= 0) {
            $sliced_keys = array_slice(array_keys($filtered_articles), $options['offset'], $options['limit'], true);
            $filtered_articles = array_intersect_key($filtered_articles, array_flip($sliced_keys));
        }

        $indexed_articles = [];
        foreach ($filtered_articles as $article) {
            $article->alias = DOCBASE . $article->alias;
            if (!empty($article->url)) $article->alias = $article->url;
            $indexed_articles[$article->id] = $article;
        }

        return $indexed_articles;
    }

    /**
     * Filters articles by month range.
     *
     * @param array $articles
     * @param int|null $startMonth
     * @param int|null $endMonth
     * @return array Filtered articles.
     */
    public function filterArticlesByMonth($articles, $startMonth = null, $endMonth = null)
    {
        $filtered_articles = array_filter($articles, function($article) use ($startMonth, $endMonth) {
            $publish_date = !is_null($article->publish_date) ? $article->publish_date : $article->add_date;

            return $article->checked == 1 &&
                   ($article->publish_date === null || $article->publish_date <= time()) &&
                   ($article->unpublish_date === null || $article->unpublish_date > time()) &&
                   ($startMonth === null || $publish_date >= $startMonth) &&
                   ($endMonth === null || $publish_date <= $endMonth) &&
                   $article->lang == PMS_LANG_ID &&
                   (empty($article->show_langs) || preg_match('/(^|,)'.PMS_LANG_ID.'(,|$)/', $article->show_langs)) &&
                   (empty($article->hide_langs) || !preg_match('/(^|,)'.PMS_LANG_ID.'(,|$)/', $article->hide_langs));
        });

        $indexed_articles = [];
        foreach ($filtered_articles as $article) {
            $indexed_articles[$article->id] = $article;
        }

        return $indexed_articles;
    }

    /**
     * Gets the count of articles per month for the specified page.
     *
     * @param int $pageId
     * @return array Associative array of article counts per month.
     */
    public function getArticlesCountByMonth($pageId)
    {
        $months = [];
        $end_month = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
        
        $filtered_articles = $this->filterArticlesByMonth(
            $this->getFilteredArticles(['id_page' => $pageId]), 
            null, 
            $end_month
        );

        foreach ($filtered_articles as $article) {
            $publish_date = !is_null($article->publish_date) ? $article->publish_date : $article->add_date;
            $month_timestamp = mktime(0, 0, 0, date('n', $publish_date), 1, date('Y', $publish_date));

            if (!isset($months[$month_timestamp])) {
                $months[$month_timestamp] = 1;
            } else {
                $months[$month_timestamp]++;
            }
        }

        return $months;
    }
}