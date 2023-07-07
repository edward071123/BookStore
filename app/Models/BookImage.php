<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookImage extends Model
{
    protected $table = 'book_images';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'book_id',
        'name',
        'path',
        'created_at'
    ];

    public $timestamps = false;
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at'        => 'datetime:Y-m-d H:i:s',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }
}
