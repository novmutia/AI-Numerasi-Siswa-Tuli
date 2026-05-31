<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/School.php
// ══════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'city'];

    public function students() { return $this->hasMany(Student::class); }
}
