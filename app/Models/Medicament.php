<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Medicament extends Model
{
    protected $table = 'medicament';
    public $timestamp = false;
    protected $primaryKey = 'id_medicament';
    protected $fillable = ['id_medicament', 'id_famille', 'depot_legal', 'nom_commercial', 'composition', 'effets', 'contre_indication', 'prix_echantillon'];

    public function famille() {
        return $this->belongsTo('App\Models\famille', 'famille', 'id_famille','id_famille');
    }
}
