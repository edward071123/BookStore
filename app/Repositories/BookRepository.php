<?php
namespace App\Repositories;

use App\Repositories\AbstractRepository;
use App\Models\Book;

class BookRepository extends AbstractRepository
{
    /** @var Book $model Model */
    protected $model;

    /**
     * Repository constructor.
     *
     * @param Book $model
     */
    public function __construct(Book $model)
    {
        $this->model     = $model;
        parent::__construct();
    }

    public function pageList($limit, $memberId)
    {
        return $this->model
            ->where('member_id', $memberId)
            ->with(['images'])
            ->paginate($limit);
    }

    public function one($id, $memberId)
    {
        return $this->model
            ->where('member_id', $memberId)
            ->where('id', $id)
            ->with(['images'])
            ->first();
    }
}
