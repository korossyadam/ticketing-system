<?php

use common\models\Ticket;
use common\models\User;
use frontend\models\NewCommentForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var NewCommentForm $commentForm */
/** @var Ticket $ticket */

$this->title = $ticket->id . '. hibajegy';
?>

<!-- Title and date of ticket -->
<div class="bg-title">

    <?php

    // Save title of ticket in a variable
    $title = "<p class='title'>$ticket->title";

    // Append 'opened' or 'closed' to the end of the title
    if ($ticket->is_closed) {
        $closedByUser = User::findOne($ticket->closed_by);
        // If User is not found by 'closed_by' attribute, the Ticket was closed by a script
        if (is_object($closedByUser)) {
            $closedBy = $closedByUser->username;;
        } else {
            $closedBy = ' Lezáró Script';
        }
        $title .= "<span style='color: #bfa85c'> (lezárta: $closedBy)</span></p>";
    } else {
        $title .= "<span style='color: #55944d'> (nyitva)</span></p>";
    }

    // Save date of ticket in a variable
    $ticketDate = $ticket->created_at;

    // Display title and date
    echo $title;
    echo "<p class='date'>$ticketDate</p>";

    ?>

</div>

<!-- The actual conversation -->
<div class="bg-ticket">

    <?php foreach ($ticket->comments as $key => $comment){
        $commentDate = $comment->created_at;
        $customer = $comment->customer;         // Admins are still considered customers

        // Decide which side the comment-bubble will be, depending on the admin status of the author
        if (!$customer->is_admin){

            // Check whether there is an image-url attached to the comment
            if ($comment->img_url !== null) {
                $basePath = Url::base(true) . '/' . $comment->img_url;
                echo "<div class='comment-right'><img class='comment-images' id='comment-image' src='$basePath' alt='<Kép>'>$comment->text</div>";
            } else {
                echo "<div class='comment-right'>$comment->text</div>";
            }

            // Echo Comment details
            echo "<div class='full-div'></div>";
            echo "<p class='details-right'>$customer->username, $commentDate</p>";

            // Echo other Comment activity: closing or re-opening the ticket
            if ($comment->closed_ticket) {
                echo "<p class='activity-right'>$customer->username <span class='green'>lezárta</span> a hibajegyet.</p>";
            } else if ($comment->reopened_ticket) {
                echo "<p class='activity-right'>$customer->username <span class='green'>újranyitotta</span> a hibajegyet.</p>";
            }

        } else {

            // Check whether there is an image-url attached to the comment
            if ($comment->img_url !== null) {
                $basePath = Url::base(true) . $comment->img_url;
                echo "<div class='comment-left'><img class='comment-images' id='comment-image' src='$basePath' alt='<Kép>'>$comment->text</div>";
            } else {
                echo "<div class='comment-left'>$comment->text</div>";
            }

            // Echo Comment details
            echo "<div class='full-div'></div>";
            echo "<p class='details-left'>$customer->username, $commentDate</p>";

            // Echo other Comment activity: closing or re-opening the ticket
            if ($comment->closed_ticket) {
                echo "<p class='activity-left'>$customer->username <span class='green'>lezárta</span> a hibajegyet.</p>";
            } else if ($comment->reopened_ticket) {
                echo "<p class='activity-left'>$customer->username <span class='green'>újranyitottta</span> a hibajegyet.</p>";
            }
        }
    } ?>

</div>

<!-- The Modal for enlarged image -->
<div id="modal" class="modal">

    <!-- Close button -->
    <span class="modal-close">&times;</span>

    <!-- Modal content (the image itself) -->
    <img class="modal-content" id="modal-image">

    <!-- Modal caption (the image text) -->
    <div id="caption"></div>
</div>

<!-- Create-comment section -->
<div class="bg-comment">

    <div class="row">
        <div class="col-lg-5 left-push">
            <?php $form = ActiveForm::begin(['id' => 'new-ticket-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($commentForm, 'text')->textarea(['class' => 'comment-body', 'rows' => 4])->label('') ?>

            <div class="options-position">
                <?= $form->field($commentForm, 'imageFile')->fileInput()->label('Kép feltöltése') ?>

                <?= $form->field($commentForm, 'close_ticket')->checkbox(['selected' => $commentForm->close_ticket])->label('Hibajegy lezárása'); ?>

                <div class="form-group">
                    <?= Html::submitButton('Hozzászólás elküldése', ['class' => 'btn btn-primary btn-comment green-bcg', 'name' => 'contact-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<script>
    // Create reference to Modal
    var modal = document.getElementById('modal');

    // Create multiple references for multiple images
    var images = document.getElementsByClassName('comment-images');

    // The image in the Modal
    var modalImg = document.getElementById("modal-image");

    // Go through all of the images with our custom class
    for (var i = 0; i < images.length; i++) {
        var img = images[i];

        // Attach our click listener for this image.
        img.onclick = function(evt) {
            modal.style.display = "block";
            modalImg.src = this.src;
        }
    }

    var span = document.getElementsByClassName("modal-close")[0];
    span.onclick = function() {
        modal.style.display = "none";
    }
</script>