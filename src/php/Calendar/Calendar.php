<?php

/**
 * Class to display a calendar in grid format
 * When constructing, the start and end date are supplied and the
 * calendar will "round up and down" the calendar. I.e. if the start
 * date supplied is 15 Jan 2021, the calendar will start at 1 Jan 2021.
 * Similary for end date. If it is given as 2 March 2021, the calendar
 * will display to 32 March 2021.
 */
namespace Titan21\SportingInfluence\Calendar;

class Calendar {

  private $from_date;
  private $to_date;

  private $add_pre_padding = true;
  private $add_post_padding = true;

  /**
   * @param int $from_date The date the calendar starts as a Unix Timestamp
   * @param int $to_date The date the calendar ends as a Unix Timestamp
   */
  public function __construct($from_date, $to_date) {
    //$from_date = strtotime($from_date);
    //$to_date = strtotime($to_date);

    if($from_date && $to_date) {
      $this->from_date = \DateTime::createFromFormat("U", $from_date);
      $this->to_date = \DateTime::createFromFormat("U", $to_date);
    } else {
      die("date is invalid");
    }
  }

  /**
   * Outputs the HTML
   */
  public function display() {
    $from_year = $this->from_date->format("Y");
    $to_year = $this->to_date->format("Y");

    $classes = ['ec_calendar'];
    if(has_filter("ec_calendar_classes")) {
      $classes = apply_filters('ec_calendar_classes', $classes);
    }
    ?>
    <div class="<?php echo implode(" ", $classes) ?>">
    <?php echo $this->displayDayOfWeekHeader(); ?>
    <?php

    if($this->add_pre_padding) {
      $from_month = $this->from_date->format("n");
      $first_of_month = \DateTime::createFromFormat("j-n-Y", "1-{$from_month}-{$from_year}");
      $from_weekday = getdate($first_of_month->format("U"))['wday'];
      for($pad = 0;$pad < $from_weekday; $pad++) {
        ?>
        <?php
        $classes = 'ec_spacer';
        if($pad == 0 || $pad == 6) $classes = 'ec_spacer ec_weekend'
        ?>
        <div class="<?php echo $classes ?>"></div>
        <?php
      }
      $this->add_pre_padding = false;
    }

    for($year = $from_year; $year <= $to_year; $year++) {
      $this->displayYear($year);
    }

    if($this->add_post_padding) {
      $to_month = $this->to_date->format("n");
      $first_of_month = \DateTime::createFromFormat("j-n-Y", "1-{$to_month}-{$to_year}");
      $from_weekday = getdate($first_of_month->format("U"))['wday'];
      for($pad = $from_weekday;$pad < 7; $pad++) {
        ?>
        <?php
        $classes = 'ec_spacer';
        if($pad == 0 || $pad == 6) $classes = 'ec_spacer ec_weekend'
        ?>
        <div class="<?php echo $classes ?>"></div>
        <?php
      }
    }
    ?>
    </div>
    <?php
  }

  /**
   * @param int $day
   * @param int $month
   * @param int $year
   * 
   * @return null
   */
  private function displayDay($day, $month, $year) {
    $classes = ['ec_day'];

    $datetime = \DateTime::createFromFormat("Y-m-d", "{$year}-{$month}-{$day}");
    $dayofweek = $datetime->format("N");

    if($dayofweek == 7 || $dayofweek == 6)
    {
      $classes[] = 'ec_weekend';
    }

    if(has_filter("ec_day_classes")) {
      $classes = apply_filters('ec_day_classes', $classes, $day, $month, $year);
    }
    $date = [
      "day" => $day,
      "month" => $month,
      "year" => $year
    ];
    ?>
    <div class="<?php echo implode(" ", $classes) ?>" data-date='<?php echo json_encode($date, JSON_FORCE_OBJECT) ?>'>
    <?php
    if(has_action("ec_day_content")) {
      do_action("ec_day_content", $day, $month, $year);
    } else {
      echo "{$day} {$month} {$year}";
    }
    ?>
    </div>
    <?php
  }

  /**
   * @param int $month
   * @param int $year
   * 
   * @return null
   */
  private function displayMonth($month, $year) {
    $classes = ['ec_month'];
    if(has_filter("ec_month_classes")) {
      $classes = apply_filters('ec_month_classes', $classes, $month, $year);
    }
    ?>
    <div class="<?php echo implode(" ", $classes) ?>">
    <?php
    if(has_action("ec_month_content")) {
      do_action("ec_month_content", $month, $year);
    }

    $daysinmonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for($day = 1; $day <= $daysinmonth; $day++) {
      $this->displayDay($day, $month, $year);
    }

    ?>
    </div>
    <?php
  }

  /**
   * @param mixed $year
   * 
   * @return [type]
   */
  private function displayYear($year) {
    $classes = ['ec_year'];
    if(has_filter("ec_year_classes")) {
      $classes = apply_filters('ec_year_classes', $classes, $year);
    }
    ?>
    <div class="<?php echo implode(" ", $classes) ?>">
    <?php
    if(has_action("ec_year_content")) {
      do_action("ec_year_content", $year);
    }

    $from_month = 1;
    if($this->from_date->format("Y") == $year) {
      $from_month = $this->from_date->format("n");
    }

    $to_month = 12;
    if($this->to_date->format("Y") == $year) {
      $to_month = $this->to_date->format("n");
    }

    for($month = $from_month; $month <= $to_month; $month++) {
      $this->displayMonth($month, $year);
    }
    ?>
    </div>
    <?php
  }

  /**
   * @return [type]
   */
  public function displayDayOfWeekHeader() {
  ?>
    <div class="ec_dayofweek">S</div>
    <div class="ec_dayofweek">M</div>
    <div class="ec_dayofweek">T</div>
    <div class="ec_dayofweek">W</div>
    <div class="ec_dayofweek">T</div>
    <div class="ec_dayofweek">F</div>
    <div class="ec_dayofweek">S</div>
  <?php
  }
}