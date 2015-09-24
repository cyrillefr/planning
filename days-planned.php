<?php 

    
    //mandatory
    date_default_timezone_set('Europe/London');


    $numberOfMonths = 6;

    $schedule = new ScheduleDaysToRemenber($numberOfMonths);
    $schedule->printScheduledDays();
    

    /**
    * 
    */
    class ScheduleDaysToRemenber
    {

        // = number of lines in csv file
        private $numberOfMonths; 

        private $scheduledDaysArr =  array();

        private $nameCSVFile = 'meetings.csv';
           
        public function __construct($numberOfMonths)
        {
            $this->numberOfMonths = $numberOfMonths;
        }

        //iterate over the number of months
        public function getScheduledDays(){

            for($i = 0; $i < $this->numberOfMonths; $i++){
                $scheduledDays = new ScheduledDays( new DateTime('+' . $i . ' month') );
                //for that months gets datas
                $scheduledDays->getDays();
                $this->scheduledDaysArr[] = $scheduledDays;

            }

        }


        public function printScheduledDays(){

            //get Datas
            $this->getScheduledDays();

            $headersArr = array('Month', 'Mid Month Meeting Date', 'End of Month Meeting Date' );

            $list = array (
                $headersArr
            );


            for ($i=0; $i < sizeof($this->scheduledDaysArr) ; $i++) { 
                $list[] = $this->scheduledDaysArr[$i]->lineOfDays;

            }

            //simple write
            $fp = fopen($this->nameCSVFile, 'w');
            foreach ($list as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
        }
    }


    class ScheduledDays
    {

        //start date: date script execution
        private $date;

        //array of different types of days, in the array are string representing classes
        public $daysInAgenda = array('MonthDay', 'MidMonthMeetingDay', 'TestingDay');

        //dates (string) for the different types of days
        public $lineOfDays = array();

        public function __construct($date)
        {
            $this->date = $date;
        }

        
        //Since we do not have explicits objects as attribute, we must use reflection
        //to instantiate them. One can add many objects.
        //One must respect naming of functions to get things done otherwise throws an error
        public function getDays(){
            for ($i=0; $i < sizeof($this->daysInAgenda)  ; $i++) {
                $class = new ReflectionClass( $this->daysInAgenda[$i] );
                $args  = array($this->date);
                $instance = $class->newInstanceArgs($args);

                $reflectionMethod = new ReflectionMethod($this->daysInAgenda[$i], 'get' . ucfirst($this->daysInAgenda[$i]) . 'Date' );
                $rs = $reflectionMethod->invoke($instance);
                $this->lineOfDays[] = $rs;
            }

            return $this->lineOfDays;
        }

    }



    /**
    * Output: 2015-09-14(Monday)
    *
    */
    class MidMonthMeetingDay
    {
        
        //start date
        private $date;
        //date where meeting realy takes place
        private $strFinalMidMonthMeetingDate;
        //nth day of the week where meeting day should take place
        private $nthDay = 14;

        public function __construct($date)
        {
            $this->date = $date;
        }

        public function getMidMonthMeetingDayDate(){

            $strDateAtNthDay = $this->date->format('Y') . '-' . $this->date->format('m') . '-' . $this->nthDay;
            $dateAtNthDay = new DateTime($strDateAtNthDay);
            $dayOfWeek = $dateAtNthDay->format('l');


            if( in_array($dayOfWeek, array('Saturday', 'Sunday')) ) {
                $dateAtNthDay = new DateTime('next Monday ' . $strDateAtNthDay );
            }
            //echo 'PROCHAIN : '. $dateAtNthDay->format('Y-m-d(l)') . PHP_EOL;

            $this->strFinalMidMonthMeetingDate = $dateAtNthDay->format('Y-m-d(l)');
            

            return $this->strFinalMidMonthMeetingDate;
        }

    }




    /**
    * Output: 2015-09-14(Monday)
    *
    */
    class TestingDay
    {

        //start date
        private $date;
        //date where testing realy takes place
        private $strFinalTestingDayDate;
        
        public function __construct($date)
        {
            $this->date = $date;
        }

        public function getTestingDayDate(){

            //July, March
            $strFullTextualMonth = $this->date->format('F');
            // '2015-01-02'
            $strDate =  $this->date->format('Y') . '-' . $this->date->format('m') . '-' . $this->date->format('d');

            //testing day takes place last day of month
            $finalTestingDayDate = new DateTime( 'last day of' . $strDate);         

            echo 'dernier jour du mois: ' . $finalTestingDayDate->format('l') . PHP_EOL;
            //full textual day of the last day of the month date
            $strLastDayOfMonth = $finalTestingDayDate->format('l');

            //last day in number: 30, 31
            $lastDayDigits = $finalTestingDayDate->format('d');

            if( in_array($strLastDayOfMonth, array('Friday', 'Saturday', 'Sunday')) ) {
                 $finalTestingDayDate = 
                    new DateTime('last Thursday ' . $this->date->format('Y') . '-' . $this->date->format('m') . '-' .  $lastDayDigits);
            }     

            $this->strFinalTestingDayDate = $finalTestingDayDate->format('Y-m-d(l)');

            //echo 'TESTING: ' . $this->strFinalTestingDayDate . PHP_EOL;

            return $this->strFinalTestingDayDate;                

        }

    }



    /**
    * Output: full text month: July, Larch
    *
    */
    class MonthDay
    {

        //start date
        private $date;

        public function __construct($date)
        {
            $this->date = $date;
        }
        

        public function getMonthDayDate(){
            //July, March
            $strFullTextualMonth = $this->date->format('F');

            return $strFullTextualMonth;
        }
    }


    
  

?>