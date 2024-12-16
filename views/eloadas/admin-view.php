<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Eloadas $eloadas */
/** @var array $bookedSeats */

$this->title = 'Admin nézet: ' . $eloadas->title . '.';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['eloadas/index']];
$this->params['breadcrumbs'][] = $this->title;

$bookedSeatsArray = [];
foreach ($bookedSeats as $seat) {
    $seatKey = $seat['seat_row'] . '-' . $seat['seat_number'];
    $bookedSeatsArray[$seatKey] = true;
}

$ticketPrice = $eloadas->ticket_price;
$ticketsSold = count($bookedSeats);

// Define seat layout and starting numbers per row
$rows = [
    4 => ['count' => 14, 'start' => 27],
    3 => ['count' => 14, 'start' => 13],
    2 => ['count' => 6, 'start' => 7],
    1 => ['count' => 6, 'start' => 1],
];

?>

<h1><?= Html::encode($this->title) ?></h1>

<!-- Container for Movie Details and Sold Tickets Side-by-Side -->
<div class="info-container">
    <!-- Movie Details Section -->
    <div class="movie-details-box">
        <p><strong>Film címe:</strong> <?= Html::encode($eloadas->title) ?></p>
        <p><strong>Dátum:</strong> <?= date('Y-m-d', strtotime($eloadas->start_time)) ?></p>
        <p><strong>Kezdés:</strong> <?= date('H:i', strtotime($eloadas->start_time)) ?></p>
        <p><strong>Befejezés:</strong> <?= date('H:i', strtotime($eloadas->end_time)) ?></p>
        <p><strong>Jegy ára:</strong> <?= Html::encode($ticketPrice) ?> €</p>
    </div>

    <!-- Sold Tickets and Revenue Section -->
    <div class="sold-tickets-box">
        <p><strong>Eladott jegyek:</strong> <?= $ticketsSold ?> drb</p>
        <p><strong>Bevétel:</strong> <?= $ticketsSold * $ticketPrice ?> €</p>
    </div>
</div>
<!-- Theater Layout Section -->
<h3>Theater Layout</h3>
<div id="seat-selection" class="seat-selection admin-view-seats">
    <div class="theatre-layout">
        <!-- Vertical and Horizontal Line Containers -->
        <div class="vertical-line"></div>
        <div class="horizontal-line"></div>
        <div class="diagonal-cell">
            <div class="oszlop-label">OSZLOP</div>
            <div class="sor-label">SOR</div>
        </div>

        <table class="seat-table">
            <thead>
            <tr>
                <th class="row-label"></th>
                <?php
                // Use 14 columns representing seats A to N
                for ($seatIndex = 0; $seatIndex < 14; $seatIndex++) {
                    $seatLetter = chr(65 + $seatIndex); // A, B, C, ...
                    echo "<th class='column-label'>{$seatLetter}</th>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($rows as $rowNumber => $seatData) {
                $seatCount = $seatData['count'];
                $seatNumber = $seatData['start'];

                echo "<tr>";
                echo "<th class='row-label'>{$rowNumber}</th>";

                // Calculate empty cells before and after the seats to center them
                $emptyCellsBefore = floor((14 - $seatCount) / 2);
                $emptyCellsAfter = 14 - $seatCount - $emptyCellsBefore;

                // Render empty cells before the seats
                for ($i = 0; $i < $emptyCellsBefore; $i++) {
                    echo "<td></td>";
                }

                // Seat cells
                for ($seatIndex = 0; $seatIndex < $seatCount; $seatIndex++) {
                    $seatLetter = chr(65 + $emptyCellsBefore + $seatIndex); // Adjust seat letter

                    $seatKey = $rowNumber . '-' . $seatLetter;
                    $isBooked = isset($bookedSeatsArray[$seatKey]);
                    $seatClass = $isBooked ? 'seat booked' : 'seat available';

                    // Render seat with the assigned class
                    echo Html::tag('td', Html::tag('div', $seatNumber, ['class' => 'inner-seat']), [
                        'class' => $seatClass,
                        'data-row' => $rowNumber,
                        'data-seat' => $seatLetter,
                    ]);

                    $seatNumber++; // Increment seat number
                }

                // Render empty cells after the seats
                for ($i = 0; $i < $emptyCellsAfter; $i++) {
                    echo "<td></td>";
                }

                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Admin Buttons Section (Moved Below Theater Layout) -->
<?php if ($ticketsSold == 0): ?>
    <div class="admin-buttons">
        <?= Html::a('Update', ['eloadas/update', 'id' => $eloadas->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Delete', ['eloadas/delete', 'id' => $eloadas->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this screening?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
<?php else: ?>
    <div class="admin-buttons">
        <p> Erre a műsorra már vettek jegyet, így nem lehet törölni vagy változtatni rajta.</p>
    </div>
<?php endif; ?>
