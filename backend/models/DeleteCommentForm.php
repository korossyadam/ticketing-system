<?php

namespace backend\models;

use common\models\Comment;
use common\models\Ticket;
use Exception;
use Yii;
use yii\base\Model;

class DeleteCommentForm extends Model
{

    /**
     * This function attempts to delete a Comment
     * It also attempts to revert the 'last_comment_at' value of the Ticket the previous Comment's 'created_at' value
     * This function should only be used inside a transaction to ensure data integrity
     * @param int $ticketID The ID of the Ticket we want to update
     * @param int $commentID The ID of the Comment we want to delete
     * @throws Exception
     */
    public function deleteComment(int $ticketID, int $commentID)
    {
        // Delete the Comment
        $comment = Comment::findOne($commentID);
        $imgUrl = $comment->img_url;
        if (!$comment->delete()) {
            throw new Exception('Failed to delete comment: ' . json_encode($comment->attributes) . json_encode($comment->getErrors()));
        }

        // Delete the image file if it exists
        if ($imgUrl !== null && file_exists(Yii::getAlias('@rootAlias') . '/frontend/web/' . $imgUrl)) {
            $filePath = Yii::getAlias('@rootAlias') . '/frontend/web/' . $imgUrl;
            unlink($filePath);
        }

        // Update the Ticket's attributes
        $ticket = Ticket::findOne($ticketID);
        $ticket->last_comment_at = $ticket->comments[count($ticket->comments)-1]->created_at;
        if (!$ticket->save()) {
            throw new Exception('Failed to update ticket: ' . json_encode($ticket->attributes) . json_encode($ticket->getErrors()));
        }

    }
}
