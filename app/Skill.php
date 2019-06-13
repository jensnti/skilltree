<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Skill;
use App\Skilltree;

class Skill extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $touches = ['skilltree'];

    protected static $recordableEvents = ['created', 'deleted'];

    public function skilltree()
    {
        return $this->belongsTo(Skilltree::class);
    }

    public function path()
    {
        return "/skilltrees/{$this->skilltree->id}/skills/{$this->id}";
    }
}