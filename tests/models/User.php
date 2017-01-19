<?php

namespace Difftechnology\Tests\Models;

use Difftechnology\Notifynder\Notifable;

class User extends \Illuminate\Database\Eloquent\Model
{
    // Never do this
    protected $fillable = ['id'];
    use Notifable;
}
