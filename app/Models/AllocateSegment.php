<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocateSegment extends Model
{
    use HasFactory;
    use Blamable;

    protected $fillable = [
        'req_recid',
        'general',
        'bfs',
        'rfs_ex_pb',
        'pb',
        'pcp',
        'afs',
    ];

    // 'general',
    public function getGeneralAttribute($value)
    {
        return (int)$value;
    }
    // 'bfs',
    public function getBfsAttribute($value)
    {
        return (int)$value;
    }
    // 'rfs_ex_pb',
    public function getRfsExPbAttribute($value)
    {
        return (int)$value;
    }
    // 'pb',
    public function getPbAttribute($value)
    {
        return (int)$value;
    }
    // 'pcp',
    public function getPcpAttribute($value)
    {
        return (int)$value;
    }
    // 'afs',
    public function getAfsAttribute($value)
    {
        return (int)$value;
    }
}
