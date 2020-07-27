<?php
    declare(strict_types=1);

    class Calendar{
        private $use_date;
        private $month;
        private $year;
        private $days_in_month;
        private $start_day_in_week;
        private $db;

        public function __construct($db, $use_date=NULL){
            $this->db = $db->get_db();

            if(isset($use_date)){
                $this->use_date = $use_date;
            }
            else{
                $this->use_date = date('Y-m-d H:i:s');
            }

            $time_string = strtotime($this->use_date);
            $this->month = (int)date('m', $time_string);
            $this->year = (int)date('Y', $time_string);

            $this->days_in_month = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

            $time_string = mktime(0, 0, 0, $this->month, 1, $this->year);
            $this->start_day_in_week = (int)date('w', $time_string);

        }

        private function _loadEventData($id=NULL){
            $sql = "SELECT `event_id`, `event_tittle`, `event_desc`, `event_start`, `event_end` FROM `events`";

            if(!empty($id)){
                $sql .= "WHERE `event_id`=:id LIMIT 1";
            }
            else{
                $start_ts = mktime(0, 0, 0, $this->month, 1, $this->year);
                $end_ts = mktime(23, 59, 59, $this->month+1, 0, $this->year);
                $start_date = date('Y-m-d H:i:s', $start_ts);
                $end_date = date('Y-m-d H:i:s', $end_ts);

                $sql .= "WHERE `event_start` BETWEEN '$start_date' AND '$end_date' ORDER BY `event_start`";
            }
            try{
                $query = $this->db->prepare($sql);
                if (!empty($id)){
                    $query->bindParam(":id", $id, PDO::PARAM_INT);
                }
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                $query->closeCursor();
                return $results;
            }
            catch (Exception $e){
                die ($e->getMessage());
            }
        }

        private function _createEventObj(){
            $events = array();
            $events_database_array = $this->_loadEventData();
            foreach ($events_database_array as $event){
                $day = date('j', strtotime($event['event_start']));
                try{
                    $events[$day][] = new Event($event);
                }
                catch(Exception $e){
                    die($e->getMessage());
                }
            }
            return $events;
        }

        public function buildCalendar(){
            $cal_month = date('F Y', strtotime($this->use_date));
            define('WEEKDAYS', array('Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'));
            $html = "\n\t<h2>$cal_month</h2>";
            for($d=0, $labels=NULL; $d<7; ++$d){
                $labels .= "\n\t<li>" . WEEKDAYS[$d] . "</li>";
            }
            $html .= "\n\t<ul class=\"weekdays\">" .$labels . "\n\t</ul>";
            $html .= "\n\t<ul>";

            $events = $this->_createEventObj();
            $event_info = NULL;

            for($i=1, $c=1, $t=date('j'), $m=date('m'), $y=date('Y'); $c<=$this->days_in_month; ++$i){
                $list_start = sprintf("\n\t\t<li");
                $list_end = "\n\t\t</li>";

                if ($this->start_day_in_week<$i && $this->days_in_month>=$c){
                    $event_info = NULL;
                    if(isset($events[$c])){
                        foreach($events[$c] as $event){
                            $link = '<a>' . $event->title . '</a>';
                            $event_info .= "\n\t\t\t$link";
                        }
                    }
                    $date = sprintf("\n\t\t\t<strong>%02d</strong>", $c++);
                }
                else{
                    $date="&nbsp;";
                }
                //Jesli aktualny dzien to niedziela zacznij nowy wiersz
                $wrap = $i != 0 && $i % 7 == 0 ? "\n\t</ul>\n\t<ul>" : NULL;
                $html .= $list_start . $date . $event_info . $list_end . $wrap;
            }           
            return $html;
        }
    }
?>