<?php

class EarningItem
{
  private $id ;
  private $name ;
  private $amount ;
  private $is_taxation ;
  private $frequency ;
  private $user_id ;
  public function get_id()
  {
    return $this->id ;
  }
  public function get_name()
  {
    return $this->name ;
  }
  public function get_amount()
  {
    return $this->amount ;
  }
  public function get_taxation_type()
  {
    return ($this->is_taxation) ? '課税' : '非課税' ;
  }
  public function get_frequency()
  {
    return $this->frequency ;
  }
  public function get_info()
  {
    return $this->name . ' : ' . number_format($this->amount) . '円（' . ($this->is_taxation ? '課税' : '非課税') . '）' ;
  }
}
