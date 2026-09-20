<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'date_stuffing',
        'date_open_stack',
        'date_closing',
        'port_of_loading',
        'port_of_destination',
        'feeder_vsl_name',
        'feeder_vsl_etd',
        'feeder_vsl_eta',
        'mother_vsl_name',
        'mother_vsl_etd',
        'mother_vsl_eta',
        'duration',
        'note',
        'status',
    ];
}
