<?php

/** @var Ticket $ticket */

use common\models\Ticket;

$color = '';
if ($ticket->is_closed) {
    $color = '#bfa85c';
} else {
    $color = '#55944d';
}

?>

<!-- Displays a color based on the passed '$ticket' variable's 'closed_by' attribute -->
<div class="status-rectangle" style = "background-color: <?= $color ?>"></div>
