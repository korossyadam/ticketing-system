<?php

namespace common\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;

class NewCommentForm extends Model
{
    public $text;
    public $close_ticket;
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'required', 'message' => 'A szövegtörzs nem lehet üres.'],
            [['text'], 'string', 'min' => 0, 'max' => 255, 'tooShort' => 'A szövegtörzs hossza túl rövid.', 'tooLong' => 'A szövegtörzs hossza túl hosszú.'],
            [['close_ticket'], 'default', 'value' => 0],
            [['imageFile'], 'required', 'on' => 'create'],
            [['imageFile'], 'file', 'skipOnError' => true,
                'extensions' => 'png, jpg, gif', 'wrongExtension' => 'Csak PNG, JPG, vagy GIF kiterjesztésű képek engedélyezettek.',
                'maxSize' => 16000000, 'tooBig' => 'A feltöltött kép maximum 16 MB lehet.'
            ],
        ];
    }

    /**
     * This function creates an additional Comment on an already existing Ticket
     * Optionally closes the ticket
     * @param Ticket $ticket The Ticket the Comment has to be saved under
     * @return Ticket The Ticket is returned to be assigned to an admin if necessary
     * @throws Exception
     */
    public function createNewComment(Ticket $ticket): Ticket
    {
        $comment = new Comment();
        $comment->customer_id = Yii::$app->user->id;
        $comment->ticket_id = $ticket->id;
        $comment->text = Html::encode($this->text);
        $comment->closed_ticket = false;
        $comment->reopened_ticket = false;
        $comment->status = Comment::STATUS_ACTIVE;

        // Set dates
        $comment->created_at = date('m/d/Y h:i:s a', time());
        $comment->updated_at = date('m/d/Y h:i:s a', time());

        // Check if Comment closes or reopens the Ticket
        if ($this->close_ticket === '1') {
            if (!$ticket->is_closed) {
                $ticket->is_closed = true;
                $ticket->closed_by = $comment->customer_id;
                $comment->closed_ticket = true;
            }
        } else {
            if ($ticket->is_closed) {
                $ticket->is_closed = false;
                $ticket->closed_by = Ticket::DEFAULT_VALUE;
                $comment->reopened_ticket = true;
            }
        }

        // Attempt to save image if Comment had an image attached to it
        if ($this->imageFile !== null) {
            $comment->img_url = $this->saveImage($this->imageFile);
        }

        // Attempt to save Comment
        if (!$comment->save()) {
            throw new Exception('Failed to save comment: ' . json_encode($comment->attributes) . json_encode($comment->getErrors()));
        }

        // Update date fields of the Ticket
        $ticket->last_comment_at = date('m/d/Y h:i:s a', time());
        $ticket->updated_at = date('m/d/Y h:i:s a', time());

        // Attempt to update Ticket
        if (!$ticket->save()) {
            throw new Exception('Failed to update ticket: ' . json_encode($ticket->attributes) . json_encode($ticket->getErrors()));
        }

        return $ticket;
    }

    /**
     * This function attempts to save the image passed into it as a parameter
     * First it attempts to create a directory based on the current user's ID
     * @param UploadedFile $image The image file to save
     * @return string The path to the newly saved image file
     * @throws Exception
     */
    public function saveImage(UploadedFile $image): string
    {
        // Generate random name for image
        $imageName = $this->generateRandomString(30);

        // The directory path contains the ID of the current user
        $dirPath = Yii::getAlias('@frontend') . '/web/uploads/' . Yii::$app->user->id;

        // Attempt to create new directory (should return true if directory already exists)
        if (!FileHelper::createDirectory($dirPath, 0775, true)) {
            throw new Exception('Failed to create new directory before image upload. Directory path was: ' . $dirPath);
        } else {
            $imagePath = $dirPath . '/' . $imageName . '.' . $image->extension;

            // Attempt to save new image
            if (!$image->saveAs($imagePath)) {
                throw new Exception('Failed to save image after creating directory. Image path was: ' . $imagePath);
            }

            return 'uploads/' . Yii::$app->user->id . '/' . $imageName . '.' . $image->extension;
        }

    }

    /**
     * This function generates a random string for files to be uploaded
     * Ensures complex strings to avoid accidental overwrite and unauthorized access from URL
     * @param int $length The length of the string to be generated, a higher value is more secure
     * @return string The random string
     */
    function generateRandomString(int $length): string
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
}
