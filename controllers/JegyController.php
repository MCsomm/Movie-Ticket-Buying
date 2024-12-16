<?php

namespace app\controllers;

use app\models\Eloadas;
use app\models\Jegy;
use app\models\JegySearch;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JegyController implements the CRUD actions for Jegy model.
 */
class JegyController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Jegy models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new JegySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Jegy model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Jegy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Jegy();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Jegy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Jegy model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Jegy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Jegy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Jegy::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBook($id)
    {
        $eloadas = Eloadas::findOne($id);

        if (!$eloadas) {
            throw new NotFoundHttpException('The requested movie does not exist.');
        }

        // Calculate the time difference between now and the movie's start time
        $now = new \DateTime('now');
        $movieStartDateTime = new \DateTime($eloadas->show_date . ' ' . $eloadas->start_time);

        $interval = $now->diff($movieStartDateTime);
        $minutesUntilStart = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        // Check if the movie starts in less than 60 minutes
        if ($minutesUntilStart < 60) {
            Yii::$app->session->setFlash('error', 'Tickets cannot be purchased less than 60 minutes before the movie starts.');
            return $this->redirect(['eloadas/index']); // Redirect to a relevant page
        }

        $model = new Jegy();
        $model->eloadas_id = $id;

        if (Yii::$app->request->isPost) {
            $selectedSeats = Yii::$app->request->post('selectedSeats'); // Get selected seats

            // Log seat selection
            if (!$selectedSeats) {
                Yii::error("No seats were selected in POST data.");
                Yii::$app->session->setFlash('error', 'No seats were selected for booking.');
                return $this->redirect(['eloadas/customer-view', 'id' => $id]);
            } else {
                Yii::info("Seats selected for booking: $selectedSeats");
            }

            $selectedSeatsArray = explode(',', $selectedSeats); // Convert the comma-separated list to an array

            // Get customer details from POST request
            $customerName = Yii::$app->request->post('Jegy')['customer_name'];
            $customerEmail = Yii::$app->request->post('Jegy')['customer_email'];
            $customerPhone = Yii::$app->request->post('Jegy')['customer_phone'];

            $successfulBookings = []; // Array to track successful bookings

            foreach ($selectedSeatsArray as $seat) {
                try {
                    // Regex to safely parse row and letter (e.g., '1-A')
                    if (preg_match('/^(\d+)-([A-Z])$/', $seat, $matches)) {
                        $seatRow = $matches[1];
                        $seatLetter = $matches[2];

                        // Validate that seat_row is numeric
                        if (!is_numeric($seatRow)) {
                            Yii::error("Non-numeric value encountered for seat row: $seatRow");
                            Yii::$app->session->addFlash('error', "Invalid seat row encountered: $seatRow");
                            continue;
                        }

                        $seatRow = (int)$seatRow;

                        // Create a new Jegy instance for each seat
                        $newBooking = new Jegy();
                        $newBooking->eloadas_id = $id;
                        $newBooking->seat_row = $seatRow;
                        $newBooking->seat_number = $seatLetter;

                        // Set the combined seat identifier
                        $newBooking->seat = $seatRow . $seatLetter;

                        // Check if the seat is already booked
                        $isBooked = Jegy::find()
                            ->where([
                                'eloadas_id' => $id,
                                'seat_row' => $seatRow,
                                'seat_number' => $seatLetter,
                            ])
                            ->exists();

                        if ($isBooked) {
                            Yii::error("Seat already booked: Row {$seatRow} Seat {$seatLetter}");
                            Yii::$app->session->addFlash('error', "Seat Row {$seatRow} Seat {$seatLetter} is already booked.");
                            continue;
                        }

                        $newBooking->customer_name = $customerName;
                        $newBooking->customer_email = $customerEmail;
                        $newBooking->customer_phone = $customerPhone;

                        // Validate and save the booking
                        if ($newBooking->validate()) {
                            if ($newBooking->save()) {
                                $successfulBookings[] = $newBooking;
                            } else {
                                Yii::error("Failed to save booking for seat: Row {$seatRow} Seat {$seatLetter}");
                                Yii::error($newBooking->getErrors());
                                Yii::$app->session->addFlash('error', 'Failed to book seat: Row ' . $seatRow . ' Seat ' . $seatLetter . '. Please try again.');
                            }
                        } else {
                            Yii::error("Validation failed for seat: Row {$seatRow} Seat {$seatLetter}");
                            Yii::error($newBooking->getErrors());
                            Yii::$app->session->addFlash('error', 'Validation failed for seat: Row ' . $seatRow . ' Seat ' . $seatLetter);
                        }
                    } else {
                        Yii::error("Invalid seat format encountered: $seat");
                        Yii::$app->session->addFlash('error', "Invalid seat format encountered: $seat");
                        continue;
                    }
                } catch (\Exception $e) {
                    Yii::error("An error occurred while trying to book seat: " . $e->getMessage());
                    Yii::$app->session->addFlash('error', 'An unexpected error occurred. Please try again.');
                }
            }

            // After the loop
            if (!empty($successfulBookings)) {
                Yii::$app->session->addFlash('success', 'Vásárlásod sikeres volt.');
                return $this->redirect(['jegy/confirmation', 'bookingIds' => implode(',', array_map(function ($booking) {
                    return $booking->id;
                }, $successfulBookings))]);
            } else {
                Yii::$app->session->setFlash('error', 'No seats could be booked. Please try again.');
                return $this->redirect(['eloadas/customer-view', 'id' => $id]);
            }
        }

        // Fetch booked seats for initial rendering or GET request
        $bookedSeats = Jegy::find()
            ->where(['eloadas_id' => $id])
            ->select(['seat_row', 'seat_number'])
            ->asArray()
            ->all();

        return $this->render('book', [
            'model' => $model,
            'eloadas' => $eloadas,
            'bookedSeats' => $bookedSeats,
        ]);
    }
    public function actionConfirmation($bookingIds)
    {
        // Split the booking IDs into an array
        $bookingIdArray = explode(',', $bookingIds);

        // Find all booked tickets based on the provided IDs
        $bookedTickets = Jegy::find()
            ->where(['id' => $bookingIdArray])
            ->all();

        // If no tickets are found, set an error flash and redirect to customer view
        if (empty($bookedTickets)) {
            Yii::$app->session->setFlash('error', 'No tickets found for confirmation.');
            return $this->redirect(['eloadas/customer-view']);
        }

        // Render the confirmation view with booked ticket details
        return $this->render('confirmation', [
            'bookedTickets' => $bookedTickets,
        ]);
    }
}


