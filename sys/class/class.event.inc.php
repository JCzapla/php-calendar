<?php 
    declare(strict_types=1);

    class Event{
        public $id;
        public $title;
        public $desc;
        public $start;
        public $end;

        public function __construct($event){
            if (is_array($event)){
                $this->id = $event['event_id'];
                $this->title = $event['event_tittle'];
                $this->desc = $event['event_desc'];
                $this->start = $event['event_start'];
                $this->end = $event['event_end'];
            }
            else{
                throw new Exception("Brak danych");
            }
        }
    }