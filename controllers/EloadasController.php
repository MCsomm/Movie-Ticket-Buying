<?php

namespace app\controllers;

use app\models\Eloadas;
use app\models\EloadasSearch;
use app\models\Jegy;
use app\models\JegySearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EloadasController implements the CRUD actions for Eloadas model.
 */
class EloadasController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only authenticated users
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Eloadas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Fetch today's date
        $currentDate = date('Y-m-d');

        // Modify query to get all screenings for today (regardless of the time)
        $dailyQuery = Eloadas::find()
            ->where(['show_date' => $currentDate]); // Fetch all screenings happening today

        // Data provider for daily screenings
        $dailyDataProvider = new ActiveDataProvider([
            'query' => $dailyQuery,
            'pagination' => ['pageSize' => 10],
        ]);

        // Query to get all screenings (without any specific date filter)
        $allQuery = Eloadas::find();

        // Data provider for all screenings
        $allDataProvider = new ActiveDataProvider([
            'query' => $allQuery,
            'pagination' => ['pageSize' => 10],
        ]);

        // Search model and data provider for sold tickets
        $searchModel = new JegySearch();
        $soldTicketsDataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Render the 'index' view with all data providers
        return $this->render('index', [
            'dailyDataProvider' => $dailyDataProvider,
            'allDataProvider' => $allDataProvider,
            'searchModel' => $searchModel,
            'soldTicketsDataProvider' => $soldTicketsDataProvider,
        ]);
    }

    /**
     * Displays a single Eloadas model.
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
     * Creates a new Eloadas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Eloadas();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                Yii::$app->session->setFlash('error', 'Validation failed. Please check the input fields.');
            } elseif ($model->save()) {
                Yii::$app->session->setFlash('success', 'Movie showing created successfully.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save movie showing. Please try again.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Eloadas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Populate virtual attributes for existing record
        $startParts = explode(':', $model->start_time);
        $model->start_hour = (int)$startParts[0];
        $model->start_minute = (int)$startParts[1];
        $model->duration_minutes = (strtotime($model->show_date . ' ' . $model->end_time) - strtotime($model->show_date . ' ' . $model->start_time)) / 60;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Movie showing updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Eloadas model.
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
     * Finds the Eloadas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Eloadas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Eloadas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionCustomerView($id)
    {
        $eloadas = Eloadas::findOne($id);

        if (!$eloadas) {
            throw new NotFoundHttpException('The requested movie does not exist.');
        }

        // Count how many tickets are sold for this movie
        $ticketsSold = Jegy::find()
            ->where(['eloadas_id' => $eloadas->id])
            ->count();

        // Define the total number of available tickets
        $totalTickets = 40;

        return $this->render('customer-view', [
            'eloadas' => $eloadas,
            'ticketsSold' => $ticketsSold,
            'totalTickets' => $totalTickets,
        ]);
    }
    public function getReservedSeatsCount()
    {
        return Jegy::find()
            ->where(['eloadas_id' => $this->id])
            ->count();
    }
    public function actionAdminView($id)
    {
        // Find the Eloadas record by ID
        $eloadas = Eloadas::findOne($id);

        if (!$eloadas) {
            throw new NotFoundHttpException('The requested screening does not exist.');
        }

        // Get all the booked seats for this screening
        $bookedSeats = Jegy::find()
            ->where(['eloadas_id' => $id])
            ->select(['seat_row', 'seat_number'])
            ->asArray()
            ->all();

        // Render the admin-view, passing the Eloadas model and booked seats
        return $this->render('admin-view', [
            'eloadas' => $eloadas,
            'bookedSeats' => $bookedSeats,
        ]);
    }
}
