<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Jegy $model */
/** @var app\models\Eloadas $eloadas */
/** @var array $bookedSeats */

$this->title = 'Jegyvásárlás';

$bookedSeatsArray = [];
foreach ($bookedSeats as $seat) {
    $seatKey = $seat['seat_row'] . '-' . $seat['seat_number'];
    $bookedSeatsArray[$seatKey] = true;
}
$ticketPrice = $eloadas->ticket_price;

// Define seat layout and starting numbers per row
$rows = [
    4 => ['count' => 14, 'start' => 27],
    3 => ['count' => 14, 'start' => 13],
    2 => ['count' => 6, 'start' => 7],
    1 => ['count' => 6, 'start' => 1],
];
?>

<h1><?= Html::encode($this->title) ?></h1>

<!-- Container for Customer Information and Movie Details Side-by-Side -->
<div class="info-container">
    <!-- Customer Information Section -->
    <div class="customer-info-box">
        <?php $form = ActiveForm::begin(['id' => 'booking-form']); ?>

        <div class="customer-info-field">
            <label for="customer_name">Név:</label>
            <?= Html::activeTextInput($model, 'customer_name', ['maxlength' => true, 'placeholder' => 'Név', 'required' => true]) ?>
        </div>

        <div class="customer-info-field">
            <label for="customer_phone">Tel:</label>
            <?= Html::activeTextInput($model, 'customer_phone', ['maxlength' => true, 'placeholder' => 'Tel', 'required' => true]) ?>
        </div>

        <div class="customer-info-field">
            <label for="customer_email">Email:</label>
            <?= Html::activeTextInput($model, 'customer_email', ['maxlength' => true, 'type' => 'email', 'placeholder' => 'Email', 'required' => true]) ?>
        </div>
    </div>

    <!-- Movie Details Section -->
    <div class="movie-details-box">
        <p><strong>Film címe:</strong> <?= Html::encode($eloadas->title) ?></p>
        <p><strong>Dátum:</strong> <?= Html::encode($eloadas->show_date) ?></p>
        <p><strong>Kezdés:</strong> <?= Html::encode($eloadas->start_time) ?></p>
        <p><strong>Befejezés:</strong> <?= Html::encode($eloadas->end_time) ?></p>
        <p><strong>Jegy ára:</strong> <?= Html::encode($ticketPrice) ?> €</p>
        <p><strong>Vásárlás folyamata:</strong> adja meg az adatait, majd a kiválasztott székére kattintva jelölje be a lefoglalni kívánt ülőhelyet, majd ha kész van, nyomja meg a "fizetés" gombot.</p>
    </div>
</div>

<!-- Seat Selection Section -->
<h3>Available Seats</h3>
<div id="seat-selection" class="seat-selection interactive-seats" data-ticket-price="<?= Html::encode($ticketPrice) ?>">
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
                    $seatClass = $isBooked ? 'seat booked' : 'seat available interactive';  // Add 'interactive' for bookable seats

                    // Render seat with the assigned class
                    echo Html::tag('td', Html::tag('div', $seatNumber, ['class' => 'inner-seat']), [
                        'class' => $seatClass,
                        'data-row' => $rowNumber,
                        'data-seat' => $seatLetter,
                        'onclick' => $isBooked ? '' : 'toggleSeatSelection(this)', // Add onclick if available
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


<!-- Display Selected Seats Count and Total Cost Section -->
<h3 class="summary-info">Jegyek száma: <span id="selected-seats-count" class="summary-value">0</span> drb</h3>
<h3 id="total-cost" class="summary-info">Fizetendő összeg: <span id="total-cost-value" class="summary-value">0</span></h3>

<!-- Hidden Input for Selected Seats -->
<?= Html::hiddenInput('selectedSeats', '', ['id' => 'selected-seats-input']) ?>

<!-- Confirm Button -->
<div class="form-group">
    <?= Html::submitButton('Fizetés', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJsFile('@web/js/book.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

