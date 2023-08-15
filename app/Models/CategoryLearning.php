<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLearning extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    const DOCUMENT = 0;
    const VIDEO = 1;
    const LINK = 2;

    public function learnings()
    {
        return $this->hasMany(Learning::class, 'category_learning_id');
    }

    public function documents()
    {
        return $this->learnings()->where([['type', CategoryLearning::DOCUMENT], ['category_learning_id', $this->id]]);
    }

    public function videos()
    {
        return $this->learnings()->where([['type', CategoryLearning::VIDEO], ['category_learning_id', $this->id]]);
    }

    public function links()
    {
        return $this->learnings()->where([['type', CategoryLearning::LINK], ['category_learning_id', $this->id]]);
    }
}
