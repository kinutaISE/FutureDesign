<?php

class EarningItem
{
  private $id ;
  private $name ;
  private $amount ;
  private $is_taxation ;
  private $user_id ;
  public function get_id()
  {
    return $this->id ;
  }
  public function get_info()
  {
    return $this->name . ' : ' . number_format($this->amount) . '円（' . ($this->is_taxation ? '課税' : '非課税') . '）' ;
  }
}
