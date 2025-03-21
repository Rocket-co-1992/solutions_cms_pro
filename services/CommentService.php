<?php

namespace Pandao\Services;

class CommentService
{
    protected $pms_db;

    public function __construct($db)
    {
        $this->pms_db = $db;
    }

    /**
     * Get comments for the given item (e.g., article, page).
     *
     * @param string $itemType The type of item (e.g., 'article', 'page').
     * @param int $itemId The ID of the item.
     * @return array List of comments for the given item.
     */
    public function getComments($itemType, $itemId)
    {
        $comments = [];

        $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_comment WHERE id_item = :id_item AND item_type = :item_type AND checked = 1 ORDER BY add_date DESC');
        $stmt->bindParam(':id_item', $itemId, \PDO::PARAM_INT);
        $stmt->bindParam(':item_type', $itemType, \PDO::PARAM_STR);

        if ($stmt->execute()) {
            $comments = $stmt->fetchAll();
        }

        return $comments;
    }
}
