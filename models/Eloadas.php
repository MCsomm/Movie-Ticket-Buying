<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eloadas".
 *
 * @property int $id
 * @property string $title
 * @property string $show_date
 * @property string $start_time
 * @property string $end_time
 * @property int $ticket_price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Jegy[] $jegies
 */
class Eloadas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $start_hour;
    public $start_minute;
    public $duration_minutes;
    public static function tableName()
    {
        return 'eloadas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'show_date', 'start_hour', 'start_minute', 'duration_minutes', 'ticket_price'], 'required'],
            [['show_date'], 'date', 'format' => 'php:Y-m-d'],
            [['start_hour', 'duration_minutes', 'ticket_price'], 'integer', 'min' => 1],
            [['duration_minutes'], 'integer', 'max' => 300, 'message' => 'A movie duration cannot exceed 300 minutes.'],
            [['title'], 'string', 'max' => 255],
            ['start_minute', 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'safe'],
            ['show_date', 'validateNotLastSunday'],
            ['show_date', 'validateFutureDate'],
            ['show_date', 'validateOverlappingScreenings'],
            ['start_time', 'validateStartTime'],
            ['start_hour', 'validateStartTime'],
            ['show_date', 'validateStartTime']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'show_date' => 'Show Date',
            'start_hour' => 'Start Hour',
            'start_minute' => 'Start Minute',
            'duration_minutes' => 'Duration (in Minutes)',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'ticket_price' => 'Ticket Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Jegies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJegies()
    {
        return $this->hasMany(Jegy::class, ['eloadas_id' => 'id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Ensure we have valid numbers before any calculations
            if (!is_numeric($this->start_hour) || !is_numeric($this->start_minute) || !is_numeric($this->duration_minutes)) {
                Yii::error('Invalid time values provided for start_hour, start_minute, or duration_minutes', __METHOD__);
                return false;  // Stop saving to prevent invalid data
            }

            // Combine start_hour and start_minute to create start_time
            $this->start_time = str_pad($this->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->start_minute, 2, '0', STR_PAD_LEFT) . ':00';

            // Calculate end_time based on start_time and duration_minutes
            $startTimestamp = strtotime($this->show_date . ' ' . $this->start_time);
            if ($startTimestamp === false) {
                Yii::error('Failed to parse start time', __METHOD__);
                return false;
            }

            $endTimestamp = $startTimestamp + ($this->duration_minutes * 60); // Adding duration in seconds
            $this->end_time = date('H:i:s', $endTimestamp);

            return true; // Proceed with saving
        }

        return false;
    }
    public function getSoldTicketsCount()
    {
        return Jegy::find()->where(['eloadas_id' => $this->id])->count();
    }
    public function getReservedSeatsCount()
    {
        return Jegy::find()
            ->where(['eloadas_id' => $this->id])
            ->count();
    }

    public function calculateDurationInHours()
    {
        if ($this->start_time && $this->end_time) {
            try {
                // Create DateTime objects for start and end times, assuming the show date is the same.
                $startDateTime = new \DateTime($this->show_date . ' ' . $this->start_time);
                $endDateTime = new \DateTime($this->show_date . ' ' . $this->end_time);

                // If the end time is earlier than the start time, assume it ends the next day.
                if ($endDateTime < $startDateTime) {
                    $endDateTime->modify('+1 day');
                }

                // Calculate the interval
                $interval = $startDateTime->diff($endDateTime);

                // Convert the interval to hours
                $durationInHours = $interval->h + ($interval->i / 60);

                // Round to 1 decimal place and return
                return round($durationInHours, 1);

            } catch (\Exception $e) {
                Yii::error("Failed to calculate duration: " . $e->getMessage());
                return 0; // In case of an error, return 0
            }
        }

        return 0;  // If start_time or end_time is missing
    }
    public function validateNotLastSunday($attribute, $params)
    {
        if ($this->isLastSunday($this->$attribute)) {
            $this->addError($attribute, 'Hónap utolsó vasárnapján takarítanak. A film nem vetíthető ezen a napon.');
        }
    }

    /**
     * Checks if the given date is the last Sunday of the month.
     * @param string $date
     * @return bool
     */
    private function isLastSunday($date)
    {
        // Convert the show_date to a DateTime object
        $dateObject = new \DateTime($date);

        // Get the last day of the month from the given date
        $lastDayOfMonth = new \DateTime($dateObject->format('Y-m-t')); // 'Y-m-t' gives the last date of the month

        // Find the last Sunday of the month
        while ($lastDayOfMonth->format('N') != 7) { // 'N' gives 7 for Sunday
            $lastDayOfMonth->modify('-1 day');
        }

        // Compare if the date given is the last Sunday
        return $dateObject->format('Y-m-d') === $lastDayOfMonth->format('Y-m-d');
    }
    public function validateFutureDate($attribute, $params)
    {
        // Get the current date and time
        $currentDate = new \DateTime();
        $showDateTime = new \DateTime($this->show_date);

        // Combine date and time for a full comparison
        if (!empty($this->start_hour) && !empty($this->start_minute)) {
            $showDateTime->setTime($this->start_hour, $this->start_minute);
        }

        // If the proposed show date and time is in the past, add an error
        if ($showDateTime < $currentDate) {
            $this->addError($attribute, 'Screenings cannot be scheduled in the past.');
        }
    }
    public function validateOverlappingScreenings($attribute, $params)
    {
        if ($this->show_date && $this->start_hour !== null && $this->start_minute !== null && $this->duration_minutes !== null) {
            // New screening start and end times
            $startDateTimeStr = $this->show_date . ' ' . str_pad($this->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->start_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $startTimestamp = strtotime($startDateTimeStr);
            $endTimestamp = $startTimestamp + ($this->duration_minutes * 60);

            // Adjust for screenings that may end after midnight
            if ($endTimestamp <= $startTimestamp) {
                $endTimestamp += 86400; // Add one day in seconds
            }

            // Buffer time (1 hour)
            $bufferTime = 3600;

            // Adjusted times with buffer
            $newStartWithBuffer = $startTimestamp - $bufferTime;
            $newEndWithBuffer = $endTimestamp + $bufferTime;

            // Build the query for existing screenings
            $existingScreeningsQuery = Eloadas::find()
                ->where(['show_date' => $this->show_date]);

            // Exclude the current record in case of update
            if (!$this->isNewRecord) {
                $existingScreeningsQuery->andWhere(['!=', 'id', $this->id]);
            }

            // Fetch existing screenings
            $existingScreenings = $existingScreeningsQuery->all();

            foreach ($existingScreenings as $screening) {
                // Existing screening start and end times
                $existingStartDateTimeStr = $screening->show_date . ' ' . $screening->start_time;
                $existingStartTimestamp = strtotime($existingStartDateTimeStr);
                $existingEndTimestamp = strtotime($screening->show_date . ' ' . $screening->end_time);

                // Adjust for screenings that may end after midnight
                if ($existingEndTimestamp <= $existingStartTimestamp) {
                    $existingEndTimestamp += 86400; // Add one day in seconds
                }

                // Adjust existing screening times with buffer
                $existingStartWithBuffer = $existingStartTimestamp - $bufferTime;
                $existingEndWithBuffer = $existingEndTimestamp + $bufferTime;

                // Check for overlap
                if ($existingStartWithBuffer < $newEndWithBuffer && $existingEndWithBuffer > $newStartWithBuffer) {
                    // Overlap detected
                    $this->addError($attribute, 'Sajnos ez a film ütközik egy másik filmnek a kiszabott időtartamával + karbantartási idő.');
                    break;
                }
            }
        }
    }
    public function validateStartTime($attribute, $params)
    {
        // Ensure that all necessary attributes are provided
        if ($this->show_date && $this->start_hour !== null && $this->start_minute !== null) {
            // Ensure that the hour and minute are integers
            $startHour = (int)$this->start_hour;
            $startMinute = (int)$this->start_minute;

            // Define the limit time hour and minute (20:00)
            $limitHour = 20;
            $limitMinute = 0;

            // Debugging to see the values being compared
            Yii::debug("Validating start time with startHour={$startHour}, startMinute={$startMinute}", __METHOD__);

            // Check if the start hour is later than the limit (i.e., after 20:00)
            if ($startHour > $limitHour) {
                Yii::debug("Validation failed: startHour > limitHour (startHour: {$startHour}, limitHour: {$limitHour})", __METHOD__);
                $this->addError('start_hour', 'Movies cannot start later than 20:00.');
            } elseif ($startHour == $limitHour && $startMinute > $limitMinute) {
                Yii::debug("Validation failed: startHour == limitHour and startMinute > limitMinute (startHour: {$startHour}, limitMinute: {$limitMinute})", __METHOD__);
                $this->addError('start_hour', 'Movies cannot start later than 20:00.');
            } else {
                Yii::debug("Validation passed: (startHour: {$startHour}, startMinute: {$startMinute}) is before the limit time", __METHOD__);
            }
        } else {
            Yii::debug("Validation skipped because necessary attributes are missing (show_date: {$this->show_date}, start_hour: {$this->start_hour}, start_minute: {$this->start_minute})", __METHOD__);
        }
    }

}
