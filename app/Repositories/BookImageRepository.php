<?php
namespace App\Repositories;

use App\Repositories\AbstractRepository;
use App\Models\BookImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookImageRepository extends AbstractRepository
{
    /** @var BookImage $model Model */
    protected $model;

    /**
     * Repository constructor.
     *
     * @param BookImage $model
     */
    public function __construct(BookImage $model)
    {
        $this->model     = $model;
        parent::__construct();
    }

    public function deleterByBookId($bookId)
    {
        $this->model->where('book_id', $bookId)->delete();
    }
}
